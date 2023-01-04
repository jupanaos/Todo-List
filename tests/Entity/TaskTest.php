<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskTest extends KernelTestCase
{
    /**
     * Test task setters and getters
     * @return void
     */
    public function testTaskSetAndGet(): void
    {
        self::bootKernel();

        $testUser = (new User())
            ->setEmail('test-user@todo.fr')
            ->setPassword('test-password')
            ->setRoles(['ROLE_USER'])
            ->setUsername('test-user');

        $testTask = (new Task())
            ->setTitle('Tache test')
            ->setContent('Contenu test')
            ->setAuthor($testUser)
            ->toggle(false)
            ->setCreatedAt(new DateTime());

        $this->assertEquals('Tache test', $testTask->getTitle());
        $this->assertEquals('Contenu test', $testTask->getContent());
        $this->assertEquals(false, $testTask->isDone());
        $this->assertEquals(true, $testTask->getAuthor() instanceof User);
        $this->assertEquals(true, $testTask->getCreatedAt() instanceof DateTime);
    }
}
