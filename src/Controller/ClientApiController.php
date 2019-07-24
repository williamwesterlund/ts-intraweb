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
use App\Repository\StudentReportRepository;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class ClientApiController extends AbstractController
{   
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
        UserRepository $userRepo,
        LoggerInterface $logger
    )
    {
        $user_id = $userService->getCurrentUser()->getId();
        $user = $userRepo->findOneBy(["id" => $user_id]);
        $userHiddenClients = $user->getHiddenClients()->toArray();

        $clients = $repository->findAllWithoutTeacher();

//        dump($clients);

        $filteredClients = array_udiff($clients, $userHiddenClients,
            function ($obj_a, $obj_b) {
                return $obj_a->getId() - $obj_b->getId();
            }
        );

        dump($filteredClients);

        return new JsonResponse($serializer->serialize($filteredClients, 'json', SerializationContext::create()->enableMaxDepthChecks()), 200, [], true);
    }

    /**
     * Lists all studentReports for a specific client.
     * @Route("/api/clients/{id}/studentreports", name="get_client_student_report", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getClientStudentReports(
        StudentReportRepository $studentReportRepo, 
        SerializerInterface $serializer, 
        ClientRepository $clientRepo,
        $id)
    {   
        $client = $clientRepo->findOneBy(["id" => $id]);
        $studentReports = $studentReportRepo->findBy(["client" => $client]);

        return new JsonResponse($serializer->serialize($studentReports, 'json', SerializationContext::create()->enableMaxDepthChecks()), 200, [], true);
    }

    /**
     * Update client with new assigned teacher.
     * @Route("/api/clients/{id}/teacher", name="update_client_teacher", methods={"PUT"})
     * @return JsonResponse
     */
    public function updateClientTeacher(
        Request $request,
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

    /**
     * Creates new client.
     * @Route("/api/clients", name="post_client", methods={"POST"})
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
     * }
     * @return JsonResponse
     */
    public function postClient(
        Request $request,
        EntityManagerInterface $em
        )
    {   
        if ($content = $request->getContent()) {
            $data = json_decode($content, true);
        }

        $client = new Client();
        $client->setParentName($data["parent_name"])
            ->setStudentName($data["student_name"])
            ->setTelephone($data["telephone"])
            ->setEmail($data["email"])
            ->setAddress($data["address"])
            ->setLevel($data["level"])
            ->setSubjects($data["subjects"])
            ->setStudyPlan($data["study_plan"])
            ->setTime($data["time"]);
        
        $em->persist($client);
        $em->flush();

        // exit(\Doctrine\Common\Util\Debug::dump($data));

        return new JsonResponse(
            ['status' => 'ok'], 
            JsonResponse::HTTP_CREATED
        );
    }
}