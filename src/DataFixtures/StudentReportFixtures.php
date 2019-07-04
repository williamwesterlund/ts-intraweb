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
            $studentReport->setReport($this->faker->paragraph)
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
