<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CookieController extends AbstractController {

    /**
     * Checks if a regular user is logged in
     * @Route("/api/logout", name="get_logout", methods={"GET"})
     *
     *
     */
    public function getLogout()
    {
        $response = Response::create();
        $response->headers->clearCookie('Bearer');
        $response->setStatusCode(Response::HTTP_OK);

        return $response;
    }


}