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
        $this->createMany(NewsPost::class, 10, function(NewsPost $newsPost, $count) {
            $newsPost->setTitle($this->faker->sentence($nbWords = 6, $variableNbWords = true))
                ->setMessage($this->faker->paragraph)
                ->setAuthor($this->getRandomReference(User::class));
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
