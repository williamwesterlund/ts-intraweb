<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class ClientApiController extends AbstractController
{   
    /**
     * Lists all clients without assigned teacher.
     * @Route("/api/clients/studentprofiles", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getClientStudentProfiles(ClientRepository $repository, SerializerInterface $serializer)
    {   
        $clients = $repository->findAllWithoutTeacher();
        return new JsonResponse($serializer->serialize($clients, 'json'), 200, [], true);
    }

    /**
     * Update client with new assigned teacher.
     * @Route("/api/clients/{id}/teacher", methods={"PUT"})
     *
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
     * @Route("/api/clients", methods={"POST"})
     *
     * @return JsonResponse
     */
    public function postClient(
        Request $request, 
        LoggerInterface $logger, 
        ClientRepository $repository, 
        SerializerInterface $serializer
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
        
        $manager->persist($client);
        $manager->flush();

        // exit(\Doctrine\Common\Util\Debug::dump($data));

        return new JsonResponse(
            ['status' => 'ok'], 
            JsonResponse::HTTP_CREATED
        );
    }
}