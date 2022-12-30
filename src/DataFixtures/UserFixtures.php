<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const ADMIN_REFERENCE = 'ADMIN';
    public const USER1_REFERENCE = 'USER_1';
    public const USER2_REFERENCE = 'USER_2';
    public const ANON_REFERENCE = 'ANON';
    public const PASSWORD_TODO = 'todoco';

    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher) 
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@todo.fr')
            ->setRoles([User::ROLE_ADMIN])
            ->setUsername('admin');
        $password = $this->hasher->hashPassword($admin, $this::PASSWORD_TODO);
        $admin->setPassword($password);

        $user01 = new User();
        $user01->setEmail('user1@todo.fr')
            ->setRoles([User::ROLE_USER])
            ->setUsername('user1');
        $password = $this->hasher->hashPassword($user01, $this::PASSWORD_TODO);
        $user01->setPassword($password);

        $user02 = new User();
        $user02->setEmail('user2@todo.fr')
            ->setRoles([User::ROLE_USER])
            ->setUsername('user2');
        $password = $this->hasher->hashPassword($user02, $this::PASSWORD_TODO);
        $user02->setPassword($password);

        $anon = new User();
        $anon->setEmail('anon@todo.fr')
            ->setRoles([User::ROLE_ANONYMOUS])
            ->setUsername('anon');
        $password = $this->hasher->hashPassword($anon, $this::PASSWORD_TODO);
        $anon->setPassword($password);

        $manager->persist($admin);
        $manager->persist($user01);
        $manager->persist($user02);
        $manager->persist($anon);

        $manager->flush();

        $this->addReference(self::ADMIN_REFERENCE, $admin);
        $this->addReference(self::USER1_REFERENCE, $user01);
        $this->addReference(self::USER2_REFERENCE, $user02);
        $this->addReference(self::ANON_REFERENCE, $anon);
    }
}
