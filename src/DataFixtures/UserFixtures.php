<?php

namespace App\DataFixtures;

use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class UserFixtures extends BaseFixture implements FixtureGroupInterface
{
    private $passwordEncoder;

    public static function getGroups(): array { return ['user']; }

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(3, 'main_users', function() {
            $user = new User();
            $user->setName($this->faker->name)
                ->setEmail($this->faker->freeEmail);

            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                'engage'
            ));

            return $user;
            });

        $this->createMany(3, 'admin_users', function() {
            $user = new User();
            $user->setName($this->faker->name)
                ->setEmail($this->faker->freeEmail)
                ->setRoles(['ROLE_ADMIN']);

            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                'engage'
            ));

            return $user;
        });

        $manager->flush();
    }
}
