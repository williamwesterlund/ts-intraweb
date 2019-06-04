<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Client;

class ClientFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $client = new Client();
        $client->setParentName("Bromand Heshmati")
            ->setStudentName("Rose")
            ->setTelephone("073-6400300")
            ->setEmail("bromand55@msn.com")
            ->setAddress("Minns ej, KTH?")
            ->setLevel("Högstadiet?")
            ->setSubjects("Minns ej.")
            ->setStudyPlan("Vid behov, tills vidare.")
            ->setTime("-");
        
        $manager->persist($client);
        $manager->flush();
    }
}
