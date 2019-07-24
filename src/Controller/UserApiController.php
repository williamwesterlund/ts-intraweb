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

class UserApiController extends AbstractController
{
    /**
     * Get a user
     * @Route("/api/user", name="get_user", methods={"GET"})
     * @return JsonResponse
     */
    public function getUserInfo(
        UserService $userService,
        UserRepository $userRepo,
        SerializerInterface $serializer
    )
    {
        $user_id = $userService->getCurrentUser()->getId();
        $user = $userRepo->findOneBy(["id" => $user_id]);

        $userObj = (object) [
            'name' => $user->getName(),
            'email' => $user->getUsername()
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
     * Creates a new teacher user
     * @Route("/api/users/teacher", name="post_user_teacher", methods={"POST"})
     * Request body : {
     *  name : [string]
     *  email : [string]
     * }
     * @return JsonResponse
     */
    public function postUserTeacher(
        Request $request,
        EntityManagerInterface $em
        )
    {   
        if ($content = $request->getContent()) {
            $data = json_decode($content, true);
        }

        $user = new User();
        $user->setName($data["name"])
            ->setPassword("temp_password")
            ->setEmail($data["email"]);
        
        $em->persist($user);
        $em->flush();

        // exit(\Doctrine\Common\Util\Debug::dump($data));

        return new JsonResponse(
            ['status' => 'ok'], 
            JsonResponse::HTTP_CREATED
        );
    }

    /**
     * Creates a new admin user
     * @Route("/api/users/admin", name="post_user_admin", methods={"POST"})
     * Request body : {
     *  name : [string]
     *  email : [string]
     * }
     * @return JsonResponse
     */
    public function postUserAdmin(
        Request $request,
        EntityManagerInterface $em
        )
    {   
        if ($content = $request->getContent()) {
            $data = json_decode($content, true);
        }

        $user = new User();
        $user->setName($data["name"])
            ->setPassword("temp_password")
            ->setEmail($data["email"])
            ->setIsTeacher(false)
            ->setIsAdmin(true);
        
        $em->persist($user);
        $em->flush();

        // exit(\Doctrine\Common\Util\Debug::dump($data));

        return new JsonResponse(
            ['status' => 'ok'], 
            JsonResponse::HTTP_CREATED
        );
    }

    /**
     * Creates a new admin user
     * @Route("/api/users/hide_client/{id}", name="post_user_hide_client", methods={"POST"})
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
                ['status' => 406],
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
     * @Route("/api/users/changepassword", name="post_user_changepassword", methods={"POST"})
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
        UserPasswordEncoderInterface $passwordEncoder)
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
                ['error' => 'Old password is incorrect.'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }
    }
}