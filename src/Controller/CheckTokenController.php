<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class CheckTokenController extends AbstractController {

    /**
     * Checks if a regular user is logged in
     * @Route("/api/checkToken", name="get_checkToken", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getCheckToken()
    {
        return new JsonResponse(
            ['status' => 200],
            JsonResponse::HTTP_OK
        );
    }

    /**
     * Checks if a admin user is logged in
     * @Route("/api/admin/checkAdminToken", name="get_checkAdminToken", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getCheckAdminToken()
    {
        return new JsonResponse(
            ['status' => 200],
            JsonResponse::HTTP_OK
        );
    }
}