<?php

namespace App\DataFixtures;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\NewsPost;
use App\Entity\User;

class NewsPostFixtures extends BaseFixture implements DependentFixtureInterface
{
    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(10, 'main_newsposts', function() {
            $newsPost = new NewsPost();
            $newsPost->setTitle($this->faker->sentence($nbWords = 6, $variableNbWords = true))
                ->setMessage($this->faker->paragraph)
                ->setAuthor($this->getRandomReference('main_users'));
            return $newsPost;
        });
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
