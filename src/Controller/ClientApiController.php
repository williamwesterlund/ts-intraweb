<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\ClientRepository;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
     * @return Response
     */
    public function updateClientTeacher(Request $request, LoggerInterface $logger, ClientRepository $repository, SerializerInterface $serializer, $id)
    {   
        if ($content = $request->getContent()) {
            $data = json_decode($content, true);
        }

        exit(\Doctrine\Common\Util\Debug::dump($data));

        return new JsonResponse(
            ['status' => 'ok'], 
            JsonResponse::HTTP_CREATED
        );
    }
}