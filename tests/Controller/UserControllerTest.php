<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    /**
     * Test access to the users list without authentication
     * @return void
     */
    public function testListNoAuth(): void
    {
        $client = static::createClient();
        $client->request('GET', '/users');

        $this->assertResponseRedirects('/login');
    }

    /**
     * Test users list access as user 
     * @return void
     */
    public function testListAsUser(): void
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneBy(['email' => 'user1@todo.fr']);

        $client
            ->loginUser($testUser)
            ->request('GET', '/users');

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }
}
