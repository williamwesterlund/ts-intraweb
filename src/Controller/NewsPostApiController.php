<?php
namespace App\Controller;

use App\Entity\Likes;
use App\Repository\LikesRepository;
use App\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use App\Entity\NewsPost;
use App\Entity\Comment;
use App\Repository\NewsPostRepository;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class NewsPostApiController extends AbstractController
{

    /**
     * CRUD
     */

    /**
     * GET: Returns all newsPosts
     * @Route("/api/admin/newsposts", name="get_all_newsposts", methods={"GET"})
     * @return JsonResponse
     */
    public function getAllNewsPosts(
        NewsPostRepository $repository,
        SerializerInterface $serializer
    )
    {
        $newsPosts = $repository->findAll();
        return new JsonResponse($serializer->serialize($newsPosts, 'json', SerializationContext::create()->enableMaxDepthChecks()), 200, [], true);
    }

    /**
     * POST: Creates new newspost.
     * @Route("/api/admin/newsposts", name="post_newsposts", methods={"POST"})
     * Request body : {
     *  title : [string]
     *  message : [string]
     *  author : [string]
     * }
     * @return JsonResponse
     */
    public function postNewsPost(
        Request $request,
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        UserRepository $userRepo
    )
    {
        if ($content = $request->getContent()) {
            $data = json_decode($content, true);
        }

        if($data["title"] != "" && $data["message"] != "") {
            $newsPost = new NewsPost();
            $newsPost->setTitle($data["title"])
                ->setMessage($data["message"]);

            $user = $userRepo->findOneBy(["name" => $data["author"]]);
            if($user) {
                $newsPost->setAuthor($user);
            } else {
                return new JsonResponse(
                    ['error' => "Couldn't find author, check your input.",
                    'status' => 406],
                    JsonResponse::HTTP_NOT_ACCEPTABLE
                );
            }

            $em->persist($newsPost);
            $em->flush();

            return new JsonResponse($serializer->serialize($newsPost, 'json', SerializationContext::create()->enableMaxDepthChecks()), 200, [], true);
        } else {
            return new JsonResponse(
                ['error' => "Title and message missing, check your input.",
                    'status' => 406],
                JsonResponse::HTTP_NOT_ACCEPTABLE
            );
        }
    }

    /**
     * PUT: Updates newspost with {id}.
     * @Route("/api/admin/newsposts/{id}", name="update_newsposts", methods={"PUT"})
     * Request body : {
     *  title : [string]
     *  message : [string]
     *  author : [string]
     * }
     * @return JsonResponse
     */
    public function updateNewsPost(
        Request $request,
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        NewsPostRepository $newsPostRepo,
        UserRepository $userRepo,
        $id
    )
    {
        if ($content = $request->getContent()) {
            $data = json_decode($content, true);
        }

        if($data["title"] != "" && $data["message"] != "") {
            $newsPost = $newsPostRepo->findOneBy(["id" => $id]);
            $newsPost->setTitle($data["title"])
                ->setMessage($data["message"]);

            $user = $userRepo->findOneBy(["name" => $data["author"]]);
            if($user) {
                $newsPost->setAuthor($user);
            } else {
                return new JsonResponse(
                    ['error' => "Couldn't find author, check your input.",
                        'status' => 406],
                    JsonResponse::HTTP_NOT_ACCEPTABLE
                );
            }

            $em->persist($newsPost);
            $em->flush();

            return new JsonResponse($serializer->serialize($newsPost, 'json', SerializationContext::create()->enableMaxDepthChecks()), 200, [], true);
        } else {
            return new JsonResponse(
                ['error' => "Title and message missing, check your input.",
                    'status' => 406],
                JsonResponse::HTTP_NOT_ACCEPTABLE
            );
        }
    }

    /**
     * DELETE: delete newspost with {id}
     * @Route("/api/admin/newsposts/{id}", name="delete_newspost", methods={"DELETE"})
     * @return JsonResponse
     */
    public function deleteNewsPost(
        NewsPostRepository $repository,
        EntityManagerInterface $em,
        $id
    )
    {
        $newsPost = $repository->findOneBy(["id" => $id]);

        $em->remove($newsPost);
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
     * Returns all newsPosts with like count.
     * @Route("/api/newsposts_with_likes", name="get_all_newsposts_with_likes", methods={"GET"})
     * @return JsonResponse
     */
    public function getAllNewsPostsWithLikeCount(
        NewsPostRepository $repository, 
        SerializerInterface $serializer
        )
    {   
        $newsPosts = $repository->findAllIncludeLikes();
        return new JsonResponse($serializer->serialize($newsPosts, 'json', SerializationContext::create()->enableMaxDepthChecks()), 200, [], true);
    }

    /**
     * Returns newspost with {id}.
     * @Route("/api/newsposts/{id}", name="get_newspost", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getNewsPost(
        NewsPostRepository $repository,
        SerializerInterface $serializer,
        $id
        )
    {
        $newsPost = $repository->findOneBy(["id" => $id]);
        return new JsonResponse($serializer->serialize($newsPost, 'json', SerializationContext::create()->enableMaxDepthChecks()), 200, [], true);
    }

    /**
     * Returns comments for a specific newspost with {id}.
     * @Route("/api/newsposts/{id}/comments", name="get_newspost_comments", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getNewsPostComments(
        NewsPostRepository $repository, 
        SerializerInterface $serializer,
        $id
        )
    {   
        $newsPost = $repository->findOneBy(["id" => $id]);
        $comments = $newsPost->getComments();
        return new JsonResponse($serializer->serialize($comments, 'json', SerializationContext::create()->enableMaxDepthChecks()), 200, [], true);
    }

    /**
     * Creates a new comment for newspost with {id}
     * @Route("/api/newsposts/{id}/comments", name="post_newspost_comment", methods={"POST"})
     * Request body : {
     *  user_id : [alphanumeric]
     *  newspost_id : [alphanumeric]
     *  message : [string]
     * }
     * @return JsonResponse
     */
        public function postNewsPostComment(
            Request $request,
            SerializerInterface $serializer,
            EntityManagerInterface $em,
            UserRepository $userRepo,
            NewsPostRepository $newsPostRepo
        )
        {
            if ($content = $request->getContent()) {
                $data = json_decode($content, true);
            }

            $author = $userRepo->findOneBy(["id" => $data["user_id"]]);
            $newsPost = $newsPostRepo->findOneBy(["id" => $data["newspost_id"]]);

            $comment = new Comment();
            $comment->setMessage($data["message"])
                ->setNewsPost($newsPost)
                ->setAuthor($author);

            $em->persist($comment);
            $em->flush();

            return new JsonResponse(
                ['status' => 'ok'],
                JsonResponse::HTTP_CREATED
            );
        }

        /**
         * Creates a new like for newspost with {id} if the user haven't already liked
         * @Route("/api/newsposts/{id}/like", name="post_newspost_like", methods={"POST"})
         * Request body : {
         *  user_id : [alphanumeric]
         * }
         * @return JsonResponse
         */
        public function postNewsPostLike(
            EntityManagerInterface $em,
            UserRepository $userRepo,
            NewsPostRepository $newsPostRepo,
            LikesRepository $likesRepo,
            UserService $userService,
            LoggerInterface $logger,
            $id
        )
        {
            $user_id = $userService->getCurrentUser()->getId();
            $author = $userRepo->findOneBy(["id" => $user_id]);
            $newsPost = $newsPostRepo->findOneBy(["id" => $id]);
            $like = $likesRepo->findOneBy(["newsPost" => $newsPost, "author" => $author]);


            if($like) {
                $em->remove($like);
            } else {
                $like = new Likes();
                $like->setAuthor($author)
                    ->setNewsPost($newsPost);
                $em->persist($like);
            }
            $em->flush();

            return new JsonResponse(
                ['status' => 200],
                JsonResponse::HTTP_OK
            );
        }

        /**
         * Edit comment with {comment_id} from newspost with {id}
         * @Route("/api/newsposts/{id}/comments/{comment_id}", name="update_newspost_comment", methods={"PUT"})
         * Request body : {
         *  message : [string]
         * }
         * @return JsonResponse
         */
        public function updateNewsPostComment(
            Request $request,
            SerializerInterface $serializer,
            EntityManagerInterface $em,
            CommentRepository $commentRepo,
            $id,
            $comment_id
            )
        {
            if ($content = $request->getContent()) {
                $data = json_decode($content, true);
            }

            $comment = $commentRepo->findOneBy(["id" => $comment_id]);

            $comment->setMessage($data["message"]);

            $em->persist($comment);
            $em->flush();

            return new JsonResponse(
                ['status' => 'ok'],
                JsonResponse::HTTP_ACCEPTED
            );
        }

        /**
         * Delete comment with {comment_id} for a specific newspost with {id}
         * @Route("/api/newsposts/{id}/comments/{comment_id}", name="delete_newspost_comment", methods={"DELETE"})
         *
         * @return JsonResponse
         */
        public function deleteNewsPostComment(
            CommentRepository $commentRepo,
            SerializerInterface $serializer,
            EntityManagerInterface $em,
            $comment_id,
            $id
            )
        {
            $comment = $commentRepo->findOneBy(["id" => $comment_id]);

            $em->remove($comment);
            $em->flush();

            return new JsonResponse(
                ['status' => 'ok'],
                JsonResponse::HTTP_ACCEPTED
            );
        }
}