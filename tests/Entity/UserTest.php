<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{
    /**
     * Test user setters and getters
     * @return void
     */
    public function testUserSetAndGet(): void
    {
        self::bootKernel();

        $testUser = (new User())
            ->setEmail('test-user@todo.fr')
            ->setPassword('test-password')
            ->setRoles(['ROLE_USER'])
            ->setUsername('test-user');

        $this->assertEquals('test-user@todo.fr', $testUser->getEmail());
        $this->assertEquals('test-password', $testUser->getPassword());
        $this->assertEquals(['ROLE_USER'], $testUser->getRoles());
        $this->assertEquals('test-user', $testUser->getUserIdentifier());
    }

    /**
     * Test if tasks are reachable through user entity (getTasks())
     * @return void
     */
    public function testUserCanGetTasks(): void
    {
        self::bootKernel();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneBy(['email' => 'user1@todo.fr']);

        $this->assertEquals(true, !empty($testUser->getTasks()));
    }
}
