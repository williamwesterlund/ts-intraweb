<?php

namespace App\DataFixtures;

use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Client;

class ClientFixtures extends BaseFixture
{
    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(10, 'main_clients', function() {
            $client = new Client();
            $client->setParentName($this->faker->name)
                ->setStudentName($this->faker->name)
                ->setTelephone($this->faker->phoneNumber)
                ->setEmail($this->faker->freeEmail)
                ->setAddress($this->faker->streetName)
                ->setLevel($this->faker->randomElement($array = array("Högstadie", "År 5", "Universitet")))
                ->setSubjects($this->faker->randomElement($array = array("Matte", "Svenska", "Spanska & Engelska")))
                ->setStudyPlan($this->faker->randomElement($array = array("2 tillfällen/vecka", "Vid behov, tillsvidare", "1 tillfälle/vecka")))
                ->setTime($this->faker->randomElement($array = array("Onsdagar 17:00", "Torsdagar, Fredagar efter 16:00", "Bestämmer med lärare")));     
            return $client;
        });
        $manager->flush();
    }
}
