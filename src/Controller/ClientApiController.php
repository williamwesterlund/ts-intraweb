<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function getAllClientStudentProfiles(ClientRepository $repository, SerializerInterface $serializer)
    {   
        $clients = $repository->findAllWithoutTeacher();
        return new JsonResponse($serializer->serialize($clients, 'json', SerializationContext::create()->enableMaxDepthChecks()), 200, [], true);
    }

    /**
     * Lists all studentReports with for a specific client.
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
     * Request body : {
     *  teacher_id : [alphanumeric]
     * }
     * @return JsonResponse
     */
    public function updateClientTeacher(
        Request $request, 
        LoggerInterface $logger, 
        ClientRepository $clientRepo,
        UserRepository $userRepo,  
        SerializerInterface $serializer, 
        EntityManagerInterface $em,
        $id
        )
    {   
        if ($content = $request->getContent()) {
            $data = json_decode($content, true);
        }

        // $logger->info($id);
        // $logger->info($data["teacher_id"]);
        // exit(\Doctrine\Common\Util\Debug::dump($data));

        $client = $clientRepo->findOneBy(["id" => $id]);
        $teacher = $userRepo->findOneBy(["id" => $data["teacher_id"]]);

        $client->setTeacher($teacher);

        $em->persist($client);
        $em->flush();

        return new JsonResponse(
            ['status' => 'ok'], 
            JsonResponse::HTTP_CREATED
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
        LoggerInterface $logger, 
        ClientRepository $repository, 
        SerializerInterface $serializer,
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