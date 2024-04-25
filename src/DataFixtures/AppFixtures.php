<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    protected UserPasswordHasherInterface $passwordEncoder;

    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Fixtures for developer testing.
     *
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('test@test.com');
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'password'));
        $user->setRoles(['ROLE_USER']);

        $manager->persist($user);

        // another user for testing shared notes
        $user = new User();
        $user->setEmail('test1@test.com');
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'password'));
        $user->setRoles(['ROLE_USER']);

        $manager->persist($user);

        // user for testing unauthorised access to mercure topics
        $user = new User();
        $user->setEmail('unauthorised@test.com');
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'password'));
        $user->setRoles(['ROLE_USER']);

        $manager->persist($user);

        $manager->flush();
    }
}
