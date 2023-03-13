<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UsersFixtures extends Fixture
{
    /**
     * @var UserPasswordHasherInterface
     */
    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        UserFactory::createOne([
            'email' => 'admin@admin.com',
            'password' => $this->hasher->hashPassword(new User(), '123456'),
            'roles' => [
                'ROLE_ADMIN',
            ]
        ]);

        $manager->flush();
    }
}
