<?php
namespace App\Controller;

use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use App\Services\UserService;
use JMS\Serializer\SerializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerInterface;
use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

class UserApiController extends AbstractController
{
    /**
     * CRUD
     */

    /**
     * GET: Returns all users
     * @Route("/api/user", name="get_all_user", methods={"GET"})
     * @return JsonResponse
     */
    public function getAllUsers(
        UserRepository $repository,
        SerializerInterface $serializer
    )
    {
        $users = $repository->findAll();
        return new JsonResponse($serializer->serialize($users, 'json', SerializationContext::create()->enableMaxDepthChecks()), 200, [], true);
    }

    /**
     * POST: Creates new client.
     * @Route("/api/user", name="post_user", methods={"POST"})
     * Request body : {
     *  name : [string]
     *  email : [string]
     *  roles : [string]
     *  clients : [string]
     *  address : [string]
     *  capacity : [string]
     * }
     * @return JsonResponse
     */
    public function postUser(
        Request $request,
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        ClientRepository $clientRepo,
        UserPasswordEncoderInterface $passwordEncoder
    )
    {
        if ($content = $request->getContent()) {
            $data = json_decode($content, true);
        }

        $user = new User();
        $user->setName($data["name"])
            ->setAddress($data["address"])
            ->setCapacity($data["capacity"]);

        if (filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
            $user->setEmail($data["email"]);
        } else {
            return new JsonResponse(
                ['error' => "Invalid email, check your input.",
                    'status' => 406],
                JsonResponse::HTTP_NOT_ACCEPTABLE
            );
        }

        $user->setPassword($passwordEncoder->encodePassword(
            $user,
            'topscholar'
        ));

        if($data["roles"] == "" || $data["roles"] == "ROLE_USER" || $data["roles"] == "ROLE_ADMIN") {
            $user->setRoles([$data["roles"]]);
        }

        if(!$data["clients"] == "") {
            $clientArray = explode(", ", $data["clients"]);
            foreach ($clientArray as $client) {
                $clientObj = $clientRepo->findOneBy(["student_name" => $client]);
                if($clientObj) {
                    $user->addClient($clientObj);
                }
            }
        }

        $em->persist($user);
        $em->flush();

        return new JsonResponse($serializer->serialize($user, 'json', SerializationContext::create()->enableMaxDepthChecks()), 200, [], true);
    }

    /**
     * PUT: Creates new client.
     * @Route("/api/user/{id}", name="update_user", methods={"PUT"})
     * Request body : {
     *  name : [string]
     *  email : [string]
     *  roles : [string]
     *  clients : [string]
     *  address : [string]
     *  capacity : [string]
     * }
     * @return JsonResponse
     */
    public function updateUser(
        Request $request,
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        UserRepository $userRepo,
        ClientRepository $clientRepo,
        $id
    )
    {
        if ($content = $request->getContent()) {
            $data = json_decode($content, true);
        }

        $user = $userRepo->findOneBy(["id" => $id]);

        $user->setName($data["name"])
            ->setAddress($data["address"])
            ->setCapacity($data["capacity"]);

        if (filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
            $user->setEmail($data["email"]);
        } else {
            return new JsonResponse(
                ['error' => "Invalid email, check your input.",
                    'status' => 406],
                JsonResponse::HTTP_NOT_ACCEPTABLE
            );
        }

        if($data["roles"] == "" || $data["roles"] == "ROLE_USER" || $data["roles"] == "ROLE_ADMIN") {
            $user->setRoles([$data["roles"]]);
        }

        if(!$data["clients"] == "") {
            $existingClients = [];
            $userClientsObj = $user->getClients();
            foreach ($userClientsObj as $userClient) {
                array_push($existingClients, $userClient->getStudentName());
            }
            $clientArray = explode(", ", $data["clients"]);
            foreach ($clientArray as $client) {
                if (!in_array($client, $existingClients)) {
                    $clientObj = $clientRepo->findOneBy(["student_name" => $client]);
                    if($clientObj) {
                        $user->addClient($clientObj);
                    }
                }
            }
            foreach ($existingClients as $existingClient) {
                if (!in_array($existingClient, $clientArray)) {
                    $clientObj = $clientRepo->findOneBy(["student_name" => $existingClient]);
                    if($clientObj) {
                        $user->removeClient($clientObj);
                    }
                }
            }
        } else {
            $userClientsObj = $user->getClients();
            foreach ($userClientsObj as $client) {
                $user->removeClient($client);
            }
        }

        $em->persist($user);
        $em->flush();

        return new JsonResponse($serializer->serialize($user, 'json', SerializationContext::create()->enableMaxDepthChecks()), 200, [], true);
    }

    /**
     * DELETE: delete user with {id}
     * @Route("/api/user/{id}", name="delete_user", methods={"DELETE"})
     *
     * @return JsonResponse
     */
    public function deleteUser(
        UserRepository $repository,
        EntityManagerInterface $em,
        $id
    )
    {
        $user = $repository->findOneBy(["id" => $id]);

        $em->remove($user);
        $em->flush();

        return new JsonResponse(
            ['status' => 'ok'],
            JsonResponse::HTTP_OK
        );
    }

    /**
     * END CRUD
     */

    /**
     * Get a user
     * @Route("/api/user/info", name="get_user", methods={"GET"})
     * @return JsonResponse
     */
    public function getUserInfo(
        UserService $userService,
        UserRepository $userRepo
    )
    {
        $user_id = $userService->getCurrentUser()->getId();
        $user = $userRepo->findOneBy(["id" => $user_id]);

        $address = '';
        if($user->getAddress() != null) {
            $address = $user->getAddress();
        }

        $capacity = '';
        if($user->getCapacity() != null) {
            $capacity = $user->getCapacity();
        }

        $userObj = (object) [
            'name' => $user->getName(),
            'email' => $user->getUsername(),
            'address' => $address,
            'capacity' => $capacity
        ];

        return new JsonResponse(json_encode($userObj), 200, [], true);
    }

    /**
     * Get a users clients
     * @Route("/api/user/clients", name="get_user_clients", methods={"GET"})
     * @return JsonResponse
     */
    public function getUserClients(
        UserService $userService,
        UserRepository $userRepo,
        SerializerInterface $serializer
        )
    {
        $user_id = $userService->getCurrentUser()->getId();
        $teacher = $userRepo->findOneBy(["id" => $user_id]);
        $clients = $teacher->getClients();

        return new JsonResponse($serializer->serialize($clients, 'json', SerializationContext::create()->enableMaxDepthChecks()), 200, [], true);
    }

    /**
     * Hides a client from a user
     * @Route("/api/user/hide_client/{id}", name="post_user_hide_client", methods={"POST"})
     * Request body : {}
     *
     * @return JsonResponse
     */
    public function postUserHiddenClient(
        EntityManagerInterface $em,
        UserRepository $userRepo,
        ClientRepository $clientRepo,
        UserService $userService,
        $id
    )
    {
        $user_id = $userService->getCurrentUser()->getId();

        $teacher = $userRepo->findOneBy(["id" => $user_id]);
        $client = $clientRepo->findOneBy(["id" => $id]);
        if ($teacher->getHiddenClients()->contains($client)) {
            return new JsonResponse(
                ['error' => "Something went wrong.",
                    'status' => 406],
                JsonResponse::HTTP_NOT_ACCEPTABLE
            );
        }
        $teacher->addHiddenClient($client);

        $em->persist($teacher);
        $em->flush();

        return new JsonResponse(
            ['status' => 200],
            JsonResponse::HTTP_OK
        );
    }

    /**
     * Change user password
     * @Route("/api/user/changepassword", name="post_user_changepassword", methods={"POST"})
     * Request body : {
     *  oldPassword : [string]
     *  newPassword : [string]
     * }
     *
     * @return JsonResponse
     */
    public function postUserChangePassword(
        Request $request,
        EntityManagerInterface $em,
        UserService $userService,
        UserRepository $userRepo,
        UserPasswordEncoderInterface $passwordEncoder
    )
    {
        if ($content = $request->getContent()) {
            $data = json_decode($content, true);
        }

        $user_id = $userService->getCurrentUser()->getId();
        $user = $userRepo->findOneBy(["id" => $user_id]);

        $checkPass = $passwordEncoder->isPasswordValid($user, $data["oldPassword"]);
        if($checkPass === true) {
            $newPasswordEncoded = $passwordEncoder->encodePassword($user, $data["newPassword"]);
            $user->setPassword($newPasswordEncoded);
            $em->persist($user);
            $em->flush();
            return new JsonResponse(
                ['status' => 200],
                JsonResponse::HTTP_OK
            );
        } else {
            return new JsonResponse(
                ['error' => 'Old password is incorrect.',
                    'status' => 400],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * Change user info
     * @Route("/api/user/info", name="post_user_changeinfo", methods={"POST"})
     * Request body : {
     *  email : [string]
     *  address : [string]
     *  capacity : [string]
     * }
     *
     * @return JsonResponse
     */
    public function postUserChangeInfo(
        Request $request,
        EntityManagerInterface $em,
        UserService $userService,
        UserRepository $userRepo
    )
    {
        if ($content = $request->getContent()) {
            $data = json_decode($content, true);
        }

        $user_id = $userService->getCurrentUser()->getId();
        $user = $userRepo->findOneBy(["id" => $user_id]);
        $user->setAddress($data["address"])
            ->setCapacity($data["capacity"]);
        $em->persist($user);
        $em->flush();

        return new JsonResponse(
            ['status' => 200],
            JsonResponse::HTTP_OK
        );
    }
}