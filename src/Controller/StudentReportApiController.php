<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\StudentReportRepository;
use App\Repository\ClientRepository;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class StudentReportApiController extends AbstractController
{   
    /**
     * Lists all studentReports with for a specific client.
     * @Route("/api/studentreport/{id}", methods={"GET"})
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
        return new JsonResponse($serializer->serialize($studentReports, 'json'), 200, [], true);
    }
}