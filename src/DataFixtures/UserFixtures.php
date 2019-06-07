<?php

namespace App\DataFixtures;

use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;

class UserFixtures extends BaseFixture
{
    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(User::class, 10, function(User $user, $count) {
            $isTeacher = $this->faker->boolean(80);
            $user->setName($this->faker->name)
                ->setEmail($this->faker->freeEmail)
                ->setPassword($this->faker->password)
                ->setIsAdmin(!$isTeacher)
                ->setIsTeacher($isTeacher);
            });
        $manager->flush();
    }
}
