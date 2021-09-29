<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private UserPasswordEncoderInterface $passwordEncoder;

    private array $users = [
        ['username' => 'admin', 'pass' => 'pass'],
        ['username' => 'user', 'pass' => 'pass']
    ];

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->users as $user) {
            $newUser = new User();

            $newUser->setUsername($user['username']);
            $newUser->setPassword($this->passwordEncoder->encodePassword($newUser, $user['pass']));
            $newUser->setApiToken( bin2hex(random_bytes(60)));

            $manager->persist($newUser);
        }

        $manager->flush();
    }
}
