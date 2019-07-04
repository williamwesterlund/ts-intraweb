<?php

namespace App\DataFixtures;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\NewsPost;
use App\Entity\User;
use App\Entity\Comment;

class CommentFixtures extends BaseFixture implements DependentFixtureInterface
{
    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(10, 'main_comments', function() {
            $comment = new Comment();
            $comment->setMessage($this->faker->paragraph)
                ->setNewsPost($this->getRandomReference('main_newsposts'))
                ->setAuthor($this->getRandomReference('main_users'));
            return $comment;
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
