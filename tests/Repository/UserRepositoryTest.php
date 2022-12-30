<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserRepositoryTest extends WebTestCase
{
    /**
     * Test if a user is correctly created
     * @return void
     */
    public function testUserAdding(): void
    {
        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = (new User())
            ->setEmail('test-user@todo.fr')
            ->setPassword('test-password')
            ->setRoles(['ROLE_USER'])
            ->setUsername('test-user');

        $userRepository->add($testUser, true);

        $this->assertEquals($testUser, $userRepository->findOneBy(['email' => 'test-user@todo.fr']));
    }

    public function testUserPasswordUpgrade(): void
    {
        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneBy(['email' => 'user1@todo.fr']);
        $password = 'new-password';

        $userRepository->upgradePassword($testUser, $password);

        $this->assertEquals($password, $testUser->getPassword());
    }
}
