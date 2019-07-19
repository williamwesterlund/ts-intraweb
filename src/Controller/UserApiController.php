<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerInterface;
use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class UserApiController extends AbstractController
{   
    /**
     * Creates a new teacher user
     * @Route("/api/users/teacher", name="post_user_teacher", methods={"POST"})
     * Request body : {
     *  name : [string]
     *  email : [string]
     * }
     * @return JsonResponse
     */
    public function postUserTeacher(
        Request $request, 
        SerializerInterface $serializer, 
        EntityManagerInterface $em
        )
    {   
        if ($content = $request->getContent()) {
            $data = json_decode($content, true);
        }

        $user = new User();
        $user->setName($data["name"])
            ->setPassword("temp_password")
            ->setEmail($data["email"]);
        
        $em->persist($user);
        $em->flush();

        // exit(\Doctrine\Common\Util\Debug::dump($data));

        return new JsonResponse(
            ['status' => 'ok'], 
            JsonResponse::HTTP_CREATED
        );
    }

    /**
     * Creates a new admin user
     * @Route("/api/users/admin", name="post_user_admin", methods={"POST"})
     * Request body : {
     *  name : [string]
     *  email : [string]
     * }
     * @return JsonResponse
     */
    public function postUserAdmin(
        Request $request, 
        SerializerInterface $serializer, 
        EntityManagerInterface $em
        )
    {   
        if ($content = $request->getContent()) {
            $data = json_decode($content, true);
        }

        $user = new User();
        $user->setName($data["name"])
            ->setPassword("temp_password")
            ->setEmail($data["email"])
            ->setIsTeacher(false)
            ->setIsAdmin(true);
        
        $em->persist($user);
        $em->flush();

        // exit(\Doctrine\Common\Util\Debug::dump($data));

        return new JsonResponse(
            ['status' => 'ok'], 
            JsonResponse::HTTP_CREATED
        );
    }
}