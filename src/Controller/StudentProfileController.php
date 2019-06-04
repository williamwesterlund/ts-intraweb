<?php namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\ClientRepository;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class StudentProfileController extends AbstractController
{
    /**
     * Lists all clients without assigned teacher.
     * @Route("/clients/studentprofilessss")
     *
     * @return JsonResponse
     */
    public function getClientStudentProfiles(ClientRepository $repository, SerializerInterface $serializer)
    {   
        $clients = $repository->findAllWithoutTeacher();
        return new JsonResponse($serializer->serialize($clients, 'json'), 200, [], true);
    }
}