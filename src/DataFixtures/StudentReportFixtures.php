<?php

namespace App\DataFixtures;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\StudentReport;
use App\Entity\User;
use App\Entity\Client;

class StudentReportFixtures extends BaseFixture implements DependentFixtureInterface
{
    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(StudentReport::class, 10, function(StudentReport $studentReport, $count) {
            $studentReport->setReport($this->faker->paragraph)
                ->setTeacher($this->getRandomReference(User::class))
                ->setClient($this->getRandomReference(Client::class));
        });
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ClientFixtures::class,
            UserFixtures::class,
        ];
    }
}
