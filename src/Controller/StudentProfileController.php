<?php namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class StudentProfileController extends AbstractController
{
    /**
     * @Route("/", name="studentProfiles")
     */
    public function list()
    {
        return $this->render('studentProfile/list.html.twig', [
            'title' => 'Student Profiles',
        ]);
    }
}