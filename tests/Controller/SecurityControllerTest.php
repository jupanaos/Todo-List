<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    /**
     * Summary of testLogin
     * @covers SecurityController::login
     */
    public function testLogin(): void
    {
        $client = $this->createClient();
        $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
