<?php
namespace App\Controller;

use App\Repository\LikesRepository;
use App\Repository\NewsPostRepository;
use App\Repository\UserRepository;
use App\Services\UserService;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class LikesApiController extends AbstractController {

    /**
     * Returns likes for a user.
     * @Route("/api/likes", name="get_likes", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getLikes(
        UserRepository $userRepo,
        LikesRepository $likesRepo,
        SerializerInterface $serializer,
        UserService $userService
    )
    {
        $user_id = $userService->getCurrentUser()->getId();
        $author = $userRepo->findOneBy(["id" => $user_id]);
        $likes = $likesRepo->findBy(["author" => $author]);

        return new JsonResponse($serializer->serialize($likes, 'json', SerializationContext::create()->enableMaxDepthChecks()), 200, [], true);
    }

}