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
        $this->createMany(Comment::class, 10, function(Comment $comment, $count) {
            $comment->setMessage($this->faker->paragraph)
                ->setNewsPost($this->getRandomReference(NewsPost::class))
                ->setAuthor($this->getRandomReference(User::class));
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
