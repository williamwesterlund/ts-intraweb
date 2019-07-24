<?php
namespace App\Controller;

use App\Services\UserService;
use DateTime;
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
     * @Route("/api/studentreport", name="post_studentreport", methods={"POST"})
     * Request body : {
     *  date : [date]
     *  dateUntil : [date]
     *  student : [string]
     *  subjects : [string]
     *  performance : [alphanumeric]
     *  motivation : [alphanumeric]
     *  trajectory : [string]
     * }
     * @return JsonResponse
     */
    public function postStudentReport(
        Request $request,
        ClientRepository $clientRepo,
        UserRepository $userRepo,
        EntityManagerInterface $em,
        UserService $userService
        )
    {   
        if ($content = $request->getContent()) {
            $data = json_decode($content, true);
        }

        $user_id = $userService->getCurrentUser()->getId();
        $teacher = $userRepo->findOneBy(["id" => $user_id]);
        $client = $clientRepo->findOneBy(["student_name" => $data["student"]]);

        $date = DateTime::createFromFormat('Y-m-d h:i:s', date('Y-m-d h:i:s', strtotime($data["date"])));
        $dateUntil = DateTime::createFromFormat('Y-m-d h:i:s', date('Y-m-d h:i:s', strtotime($data["dateUntil"])));

        $studentReport = new StudentReport();
        $studentReport->setQ1Subjects($data["subjects"])
            ->setQ2Performance($data["performance"])
            ->setQ3Motivation($data["motivation"])
            ->setQ4Trajectory($data["trajectory"])
            ->setDate($date)
            ->setDateUntil($dateUntil)
            ->setTeacher($teacher)
            ->setClient($client);
        
        $em->persist($studentReport);
        $em->flush();

        return new JsonResponse(
            ['status' => 200],
            JsonResponse::HTTP_OK
        );
    }
}