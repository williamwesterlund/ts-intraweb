<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\UserRepository;
use App\Repository\ClientRepository;
use App\Entity\StudentReport;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use JMS\Serializer\SerializationContext;

class StudentReportApiController extends AbstractController
{   
    /**
     * Creates a new student report
     * @Route("/api/studentreports", name="post_studentreports", methods={"POST"})
     *
     * @return JsonResponse
     */
    public function postStudentReport(
        Request $request, 
        SerializerInterface $serializer, 
        ClientRepository $clientRepo,
        UserRepository $userRepo,
        EntityManagerInterface $em
        )
    {   
        if ($content = $request->getContent()) {
            $data = json_decode($content, true);
        }

        $teacher = $userRepo->findOneBy(["id" => $data["teacher_id"]]);
        $client = $clientRepo->findOneBy(["id" => $data["client_id"]]);

        $studentReport = new StudentReport();
        $studentReport->setReport($data["report"])
            ->setTeacher($teacher)
            ->setClient($client);
        
        $em->persist($studentReport);
        $em->flush();

        // exit(\Doctrine\Common\Util\Debug::dump($data));

        return new JsonResponse(
            ['status' => 'ok'], 
            JsonResponse::HTTP_CREATED
        );
    }
}