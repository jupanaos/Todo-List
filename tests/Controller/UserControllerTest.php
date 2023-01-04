<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    /**
     * Test access to the users list without authentication (not allowed)
     * @return void
     */
    public function testUserListNoAuth(): void
    {
        $client = static::createClient();
        $client->request('GET', '/users');

        $this->assertResponseRedirects('/login');
    }

    /**
     * Test users list access as user (not allowed)
     * @return void
     */
    public function testUserListAsUser(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneBy(['email' => 'user1@todo.fr']);

        $client
            ->loginUser($testUser)
            ->request('GET', '/users');

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    /**
     * Test users list access as admin (allowed)
     * @return void
     */
    public function testUserListAsAdmin(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testAdmin = $userRepository->findOneBy(['email' => 'admin@todo.fr']);

        $client
            ->loginUser($testAdmin)
            ->request('GET', '/users');

        $this->assertResponseIsSuccessful();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * Test user creation as user (not allowed)
     * @return void
     */
    public function testUserCreateAsUser(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneBy(['email' => 'user1@todo.fr']);

        $client
            ->loginUser($testUser)
            ->request('GET', '/users/create');

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    /**
     * Test user creation as admin (allowed)
     * @return void
     */
    public function testUserCreateAsAdmin(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testAdmin = $userRepository->findOneBy(['email' => 'admin@todo.fr']);

        $client
            ->loginUser($testAdmin)
            ->request('GET', '/users/create');
        $client->submitForm(
                'Créer l\'utilisateur', [
                    'user[username]' => 'username-test',
                    'user[plainPassword][first]' => 'todo-test',
                    'user[plainPassword][second]' => 'todo-test',
                    'user[email]' => 'email@test.fr',
                    'user[roles]' => 'ROLE_USER'
                ]
            );
        $client->followRedirects();

        $this->assertResponseRedirects('/users', 302);
        $this->assertNotNull($userRepository->findOneBy(['email' => 'email@test.fr']));
    }

    /**
     * Test user creation with an existing username
     * @return void
     */
    public function testUserCreateUsernameExists(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testAdmin = $userRepository->findOneBy(['email' => 'admin@todo.fr']);

        $client
            ->loginUser($testAdmin)
            ->request('GET', '/users/create');

        $client->submitForm(
            'Créer l\'utilisateur', [
            'user[username]' => 'user1',
            'user[plainPassword][first]' => 'todo-test',
            'user[plainPassword][second]' => 'todo-test',
            'user[email]' => 'user-exist@todo.fr',
            'user[roles]' => 'ROLE_USER',
            ]
        );

        $this->assertEquals(500, $client->getResponse()->getStatusCode());
    }

    /**
     * Test user creation with an existing email
     * @return void
     */
    public function testUserCreateEmailExists(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testAdmin = $userRepository->findOneBy(['email' => 'admin@todo.fr']);

        $client
            ->loginUser($testAdmin)
            ->request('GET', '/users/create');

        $client->submitForm(
            'Créer l\'utilisateur', [
            'user[username]' => 'user-exist',
            'user[plainPassword][first]' => 'todo-test',
            'user[plainPassword][second]' => 'todo-test',
            'user[email]' => 'user1@todo.fr',
            'user[roles]' => 'ROLE_USER',
            ]
        );

        $this->assertEquals(500, $client->getResponse()->getStatusCode());
    }

    /**
     * Test user edition as admin (allowed)
     * @return void
     */
    public function testUserEditAsAdmin(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testAdmin = $userRepository->findOneBy(['email' => 'admin@todo.fr']);
        $testUser = $userRepository->findOneBy(['email' => 'user2@todo.fr']);

        $client->loginUser($testAdmin);
        $client->request('GET', '/users/'.$testUser->getId().'/edit');

        $client->submitForm(
            'Editer', [
            'user[username]' => 'user2-edit',
            'user[plainPassword][first]' => 'todo-test',
            'user[plainPassword][second]' => 'todo-test',
            'user[email]' => 'user2-edit@todo.fr',
            'user[roles]' => 'ROLE_USER',
            ]
        );
        $client->followRedirects();

        $testUserEdited = $userRepository->find($testUser->getId());
        $this->assertResponseRedirects('/users', 302);
        $this->assertNotNull($userRepository->findOneBy(['email' => 'user2-edit@todo.fr']));
        $this->assertNull($userRepository->findOneBy(['email' => 'user2@todo.fr']));
        $this->assertSame('user2-edit', $testUserEdited->getUsername());
        $this->assertSame('user2-edit@todo.fr', $testUserEdited->getEmail());
    }
}
