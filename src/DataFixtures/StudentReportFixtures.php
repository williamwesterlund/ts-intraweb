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
        $this->createMany(10, 'main_studentreports', function() {
            $studentReport = new StudentReport();
            $studentReport->setQ1Subjects('Math')
                ->setQ2Performance($this->faker->numberBetween($min = 1, $max = 5))
                ->setQ3Motivation($this->faker->numberBetween($min = 1, $max = 5))
                ->setQ4Trajectory($this->faker->paragraph)
                ->setDate($this->faker->dateTime)
                ->setDateUntil($this->faker->dateTime)
                ->setTeacher($this->getRandomReference('main_users'))
                ->setClient($this->getRandomReference('main_clients'));
            return $studentReport;
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
