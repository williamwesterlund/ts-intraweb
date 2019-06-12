<?php
namespace App\Controller;

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
     * Returns all newsPosts.
     * @Route("/api/newsposts", name="get_all_newsposts", methods={"GET"})
     *
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
     * Creates a new NewsPost
     * @Route("/api/newsposts", name="post_newspost", methods={"POST"})
     * Request body : {
     *  user_id : [alphanumeric]
     *  title : [string]
     *  message : [string]
     * }
     * @return JsonResponse
     */
    public function postNewsPost(
        Request $request, 
        SerializerInterface $serializer, 
        EntityManagerInterface $em,
        UserRepository $userRepo
        )
    {   
        if ($content = $request->getContent()) {
            $data = json_decode($content, true);
        }

        $author = $userRepo->findOneBy(["id" => $data["user_id"]]);

        $newsPost = new NewsPost();
        $newsPost->setTitle($data["title"])
            ->setMessage($data["message"])
            ->setAuthor($author);
        
        $em->persist($newsPost);
        $em->flush();

        return new JsonResponse(
            ['status' => 'ok'], 
            JsonResponse::HTTP_CREATED
        );
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
     * Edit newspost with {id}
     * @Route("/api/newsposts/{id}", name="update_newspost", methods={"PUT"})
     * Request body : {
     *  title : [string]
     *  message : [string]
     * }
     * @return JsonResponse
     */
    public function updateNewsPost(
        Request $request, 
        SerializerInterface $serializer, 
        EntityManagerInterface $em,
        NewsPostRepository $newsPostRepo,
        $id
        )
    {   
        if ($content = $request->getContent()) {
            $data = json_decode($content, true);
        }

        $newsPost = $newsPostRepo->findOneBy(["id" => $id]);

        $newsPost->setTitle($data["title"]);
        $newsPost->setMessage($data["message"]);

        $em->persist($newsPost);
        $em->flush();

        return new JsonResponse(
            ['status' => 'ok'], 
            JsonResponse::HTTP_ACCEPTED
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
     * Delete newspost with {id}
     * @Route("/api/newsposts/{id}", name="delete_newspost", methods={"DELETE"})
     *
     * @return JsonResponse
     */
    public function deleteNewsPost(
        NewsPostRepository $repository, 
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        $id
        )
    {   
        $newsPost = $repository->findOneBy(["id" => $id]);

        $em->remove($newsPost);
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