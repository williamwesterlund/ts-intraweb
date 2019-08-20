<?php
namespace App\Controller;

use App\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mercure\Publisher;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use App\Entity\Client;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class ClientApiController extends AbstractController
{

    /** GET: Certbot
     * @Route("/.well-known/acme-challenge/vlBCfK4Wfr-4AIZg5Rq0_tsfoQL0UVWpbqGSz8tJGvA", name="get_certbot", methods={"GET"})
     * @return Response
     */
    public function getLetsEncrypt()
    {
        $data = "vlBCfK4Wfr-4AIZg5Rq0_tsfoQL0UVWpbqGSz8tJGvA.5nPnBVKjanQcn9m0OkeSOJpw-hICdgxchUZbd94njIo";
        return new Response(
            '<html><body>'.$data.'</body></html>'
        );
    }

    /**
     * CRUD
     */

    /**
     * GET: Returns all clients
     * @Route("/api/admin/clients", name="get_all_client", methods={"GET"})
     * @return JsonResponse
     */
    public function getAllClients(
        ClientRepository $repository,
        SerializerInterface $serializer
    )
    {
        $clients = $repository->findAll();
        return new JsonResponse($serializer->serialize($clients, 'json', SerializationContext::create()->enableMaxDepthChecks()), 200, [], true);
    }

    /**
     * POST: Creates new client.
     * @Route("/api/admin/clients", name="post_client", methods={"POST"})
     * Request body : {
     *  parent_name : [string]
     *  student_name : [string]
     *  telephone : [string]
     *  email : [string]
     *  address : [string]
     *  level : [string]
     *  subjects : [string]
     *  study_plan : [string]
     *  time : [string]
     *  teacher : [string]
     *  hidden_users : [string]
     * }
     * @return JsonResponse
     */
    public function postClient(
        Request $request,
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        UserRepository $userRepo
    )
    {
        if ($content = $request->getContent()) {
            $data = json_decode($content, true);
        }

        $client = new Client();
        $client->setParentName($data["parent_name"])
            ->setStudentName($data["student_name"])
            ->setTelephone($data["telephone"])
            ->setAddress($data["address"])
            ->setLevel($data["level"])
            ->setSubjects($data["subjects"])
            ->setStudyPlan($data["study_plan"])
            ->setTime($data["time"]);

        if (filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
            $client->setEmail($data["email"]);
        } else {
            return new JsonResponse(
                ['error' => "Invalid email, check your input.",
                    'status' => 406],
                JsonResponse::HTTP_NOT_ACCEPTABLE
            );
        }

        if($data["teacher"] == "") {
            $client->setTeacher(null);
        } else {
            $teacher = $userRepo->findOneBy(["name" => $data["teacher"]]);
            if($teacher) {
                $client->setTeacher($teacher);
            } else {
                return new JsonResponse(
                    ['error' => "Couldn't find teacher, check your input.",
                        'status' => 400],
                    JsonResponse::HTTP_BAD_REQUEST
                );
            }
        }

        if($data["hidden_users"] != "") {
            $hiddenUsersArray = explode(", ", $data["hidden_users"]);
            foreach ($hiddenUsersArray as $hiddenUser) {
                $user = $userRepo->findOneBy(["name" => $hiddenUser]);
                if($user) {
                    $client->addHiddenUser($user);
                }
            }
        }

        $em->persist($client);
        $em->flush();

        return new JsonResponse($serializer->serialize($client, 'json', SerializationContext::create()->enableMaxDepthChecks()), 200, [], true);
    }

    /**
     * PUT: Update Client Data
     * @Route("/api/admin/clients/{id}", name="update_client", methods={"PUT"})
     * Request body : {
     *  parent_name : [string]
     *  student_name : [string]
     *  telephone : [string]
     *  email : [string]
     *  address : [string]
     *  level : [string]
     *  subjects : [string]
     *  study_plan : [string]
     *  time : [string]
     *  teacher : [string]
     *  hidden_users : [string]
     * }
     * @return JsonResponse
     */
    public function updateClient(
        Request $request,
        SerializerInterface $serializer,
        ClientRepository $clientRepo,
        UserRepository $userRepo,
        EntityManagerInterface $em,
        $id
    )
    {
        if ($content = $request->getContent()) {
            $data = json_decode($content, true);
        }

        $client = $clientRepo->findOneBy(["id" => $id]);

        $client->setParentName($data["parent_name"])
            ->setStudentName($data["student_name"])
            ->setTelephone($data["telephone"])
            ->setAddress($data["address"])
            ->setLevel($data["level"])
            ->setSubjects($data["subjects"])
            ->setStudyPlan($data["study_plan"])
            ->setTime($data["time"]);

        if (filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
            $client->setEmail($data["email"]);
        } else {
            return new JsonResponse(
                ['error' => "Invalid email, check your input.",
                    'status' => 406],
                JsonResponse::HTTP_NOT_ACCEPTABLE
            );
        }

        if($data["teacher"] == "") {
            $client->setTeacher(null);
        } else {
            $teacher = $userRepo->findOneBy(["name" => $data["teacher"]]);
            if($teacher) {
                $client->setTeacher($teacher);
            } else {
                return new JsonResponse(
                    ['error' => "Couldn't find teacher, check your input.",
                    'status' => 400],
                    JsonResponse::HTTP_BAD_REQUEST
                );
            }

        }

        if($data["hidden_users"] != "") {
            $hiddenUsersArray = explode(", ", $data["hidden_users"]);
            foreach ($hiddenUsersArray as $hiddenUser) {
                $user = $userRepo->findOneBy(["name" => $hiddenUser]);
                if($user) {
                    $client->addHiddenUser($user);
                }
            }
        }

        $em->persist($client);
        $em->flush();

        return new JsonResponse($serializer->serialize($client, 'json', SerializationContext::create()->enableMaxDepthChecks()), 200, [], true);
    }

    /**
     * DELETE: delete client with {id}
     * @Route("/api/admin/clients/{id}", name="delete_client", methods={"DELETE"})
     *
     * @return JsonResponse
     */
    public function deleteClient(
        ClientRepository $repository,
        EntityManagerInterface $em,
        $id
    )
    {
        $client = $repository->findOneBy(["id" => $id]);

        $em->remove($client);
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
     * Returns all clients without assigned teacher.
     * @Route("/api/clients/studentprofiles", name="get_all_client_student_profiles", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getAllClientStudentProfiles(
        ClientRepository $repository,
        SerializerInterface $serializer,
        UserService $userService,
        UserRepository $userRepo
    )
    {
        $user_id = $userService->getCurrentUser()->getId();
        $user = $userRepo->findOneBy(["id" => $user_id]);
        $userHiddenClients = $user->getHiddenClients()->toArray();

        $clients = $repository->findAllWithoutTeacher();

        $filteredClients = array_udiff($clients, $userHiddenClients,
            function ($obj_a, $obj_b) {
                return $obj_a->getId() - $obj_b->getId();
            }
        );

        return new JsonResponse($serializer->serialize($filteredClients, 'json', SerializationContext::create()->enableMaxDepthChecks()), 200, [], true);
    }

    /**
     * Update client with new assigned teacher.
     * @Route("/api/clients/{id}/teacher", name="update_client_teacher", methods={"PUT"})
     * @return JsonResponse
     */
    public function updateClientTeacher(
        ClientRepository $clientRepo,
        UserRepository $userRepo,
        EntityManagerInterface $em,
        Publisher $publisher,
        UserService $userService,
        $id
        )
    {
        $user_id = $userService->getCurrentUser()->getId();

        $client = $clientRepo->findOneBy(["id" => $id]);
        $teacher = $userRepo->findOneBy(["id" => $user_id]);

        $client->setTeacher($teacher);

        $em->persist($client);
        $em->flush();

        $update = new Update(
            'studentprofile',
            json_encode(['status' => 'Update'])
        );

        // The Publisher service is an invokable object
        $publisher($update);

        return new JsonResponse(
            ['status' => 200],
            JsonResponse::HTTP_OK
        );
    }
}