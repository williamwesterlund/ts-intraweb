<?php
namespace App\Controller;

use App\Repository\StudentReportRepository;
use App\Services\UserService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Repository\ClientRepository;
use App\Entity\StudentReport;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;

class StudentReportApiController extends AbstractController
{
    /**
     * CRUD
     */

    /**
     * GET: Returns all student reports.
     * @Route("/api/studentreport", name="get_all_studentreport", methods={"GET"})
     * @return JsonResponse
     */
    public function getAllStudentReport(
        StudentReportRepository $repository,
        SerializerInterface $serializer
    )
    {
        $studentReports = $repository->findAll();
        return new JsonResponse($serializer->serialize($studentReports, 'json', SerializationContext::create()->enableMaxDepthChecks()), 200, [], true);
    }

    /**
     * PUT: Updates student report with {id}.
     * @Route("/api/studentreport/{id}", name="updatestudentreport", methods={"PUT"})
     * Request body : {
     *  teacher : [string]
     *  client : [string]
     *  date : [string]
     *  date_until : [string]
     *  q1_subjects : [string]
     *  q2_performance : [string
     *  q3_motivation : [string]
     *  q4_trajectory : [string]
     * }
     * @return JsonResponse
     */
    public function updateStudentReport(
        Request $request,
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        StudentReportRepository $studentReportRepo,
        UserRepository $userRepo,
        ClientRepository $clientRepo,
        $id
    )
    {
        if ($content = $request->getContent()) {
            $data = json_decode($content, true);
            $studentReport = $studentReportRepo->findOneBy(["id" => $id]);

            $teacher = $userRepo->findOneBy(["name" => $data["teacher"]]);
            $client = $clientRepo->findOneBy(["student_name" => $data["client"]]);
            if($teacher && $client) {
                $studentReport->setTeacher($teacher)
                    ->setClient($client);
            } else {
                return new JsonResponse(
                    ['error' => "Couldn't find author or teacher, check your input.",
                        'status' => 400],
                    JsonResponse::HTTP_BAD_REQUEST
                );
            }

            $regExpDatePattern = '/\d{4}-[01]\d-[0-3]\dT[0-2]\d:[0-5]\d:[0-5]\d([+-][0-2]\d:[0-5]\d|Z)/';
            if(preg_match($regExpDatePattern, $data["date"]) && preg_match($regExpDatePattern, $data["date_until"])) {
                $date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', strtotime($data["date"])));
                $dateUntil = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', strtotime($data["date_until"])));
            } else {
                return new JsonResponse(
                    ['error' => "Wrong date format, check your input.",
                        'status' => 400],
                    JsonResponse::HTTP_BAD_REQUEST
                );
            }


            $studentReport->setQ1Subjects($data["q1_subjects"])
                ->setQ4Trajectory($data["q4_trajectory"])
                ->setDate($date)
                ->setDateUntil($dateUntil);

            $q2 = intval($data["q2_performance"]);
            if(is_int($q2) && (1 <= $q2) && ($q2 <= 5)) {
                $studentReport->setQ2Performance($q2);
            } else {
                return new JsonResponse(
                    ['error' => "Performance field must be a number between 1-5.",
                        'status' => 400],
                    JsonResponse::HTTP_BAD_REQUEST
                );
            }

            $q3 = intval($data["q3_motivation"]);
            if(is_int($q3) && (1 <= $q3) && ($q3 <= 5)) {
                $studentReport->setQ3Motivation($q3);
            } else {
                return new JsonResponse(
                    ['error' => "Motivation field must be a number between 1-5.",
                        'status' => 400],
                    JsonResponse::HTTP_BAD_REQUEST
                );
            }


            $em->persist($studentReport);
            $em->flush();

            return new JsonResponse($serializer->serialize($studentReport, 'json', SerializationContext::create()->enableMaxDepthChecks()), 200, [], true);
        } else {
            return new JsonResponse(
                ['error' => "Something went wrong, try again.",
                    'status' => 400],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * DELETE: delete student report with {id}
     * @Route("/api/studentreport/{id}", name="delete_studentreport", methods={"DELETE"})
     * @return JsonResponse
     */
    public function deleteStudentReport(
        StudentReportRepository $repository,
        EntityManagerInterface $em,
        $id
    )
    {
        $studentReport = $repository->findOneBy(["id" => $id]);

        $em->remove($studentReport);
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
     * Creates a new student report
     * @Route("/api/studentreport", name="post_studentreport", methods={"POST"})
     * Request body : {
     *  date : [string]
     *  dateUntil : [string]
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

            $user_id = $userService->getCurrentUser()->getId();
            $teacher = $userRepo->findOneBy(["id" => $user_id]);
            $client = $clientRepo->findOneBy(["student_name" => $data["student"]]);

            $date = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', strtotime($data["date"])));
            $dateUntil = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', strtotime($data["dateUntil"])));

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
        } else {
            return new JsonResponse(
                ['error' => "Something went wrong, try again.",
                    'status' => 400],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }
    }
}