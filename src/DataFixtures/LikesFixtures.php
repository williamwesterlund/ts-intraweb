<?php

namespace App\DataFixtures;

use App\Entity\Likes;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class LikesFixtures extends BaseFixture implements DependentFixtureInterface
{
    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(10, 'main_likes', function() {
            $like = new Likes();
            $like->setNewsPost($this->getRandomReference('main_newsposts'))
                ->setAuthor($this->getRandomReference('main_users'));
            return $like;
        });
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            NewsPostFixtures::class,
        ];
    }
}
