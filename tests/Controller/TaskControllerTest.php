<?php

namespace App\Tests\Controller;

use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    /**
     * Test access to the tasks list without authentication (not allowed)
     * @return void
     */
    public function testTaskListNoAuth(): void
    {
        $client = static::createClient();
        $client->request('GET', '/tasks');

        $this->assertResponseRedirects('/login');
    }

    /**
     * Test tasks list access as user (allowed)
     * @return void
     */
    public function testTaskListAsUser(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneBy(['email' => 'user1@todo.fr']);

        $client
            ->loginUser($testUser)
            ->request('GET', '/tasks');

        $this->assertResponseIsSuccessful();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * Test task creation with authentication
     * @return void
     */
    public function testTaskCreate(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $testUser = $userRepository->findOneBy(['email' => 'user1@todo.fr']);

        $client
            ->loginUser($testUser)
            ->request('GET', '/tasks/create');
        $client->submitForm(
                'Publier', [
                    'task[title]' => 'Tâche test',
                    'task[content]' => 'Ceci est une tâche test'
                ]
            );
        $client->followRedirects();

        $this->assertResponseRedirects('/tasks', 302);
        $this->assertNotNull($taskRepository->findOneBy(['title' => 'Tâche test']));
    }

    /**
     * Test task edit with permission (authenticated user is the task's author)
     * @return void
     */
    public function testTaskEditAllowed(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $taskRepository = static::getContainer()->get(TaskRepository::class);

        $testUser = $userRepository->findOneBy(['email' => "user1@todo.fr"]);
        $client->loginUser($testUser);

        $testTask = $taskRepository->findOneBy(['title' => "Tache user 1"]);
        $client->request('GET', '/tasks/'.$testTask->getId().'/edit');

        $client->submitForm(
            'Editer', [
            'task[title]' => 'Tache user 1 edit',
            'task[content]' => 'Tache 1 edit'
            ]
        );

        $client->followRedirects();

        $taskEdited = $taskRepository->find($testTask->getId());
        $this->assertResponseRedirects('/tasks', 302);
        $this->assertNotNull($taskRepository->findOneBy(['title' => 'Tache user 1 edit']));
        $this->assertNull($taskRepository->findOneBy(['title' => 'Tache user 1']));
        $this->assertSame('Tache user 1 edit', $taskEdited->getTitle());
        $this->assertSame('Tache 1 edit', $taskEdited->getContent());
    }

    /**
     * Test task edit with no permission (authenticated user is not the task's author)
     * @return void
     */
    public function testTaskEditNotAllowed(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $taskRepository = static::getContainer()->get(TaskRepository::class);

        $testUser = $userRepository->findOneBy(['email' => "user1@todo.fr"]);
        $client->loginUser($testUser);
        $client->followRedirects();

        $testTask = $taskRepository->findOneBy(['title' => "Tache user 2"]);
        $client->request('GET', '/tasks/'.$testTask->getId().'/edit');

        $this->assertNotEquals($testUser->getId(), $testTask->getAuthor()->getId());
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    /**
     * Test task status toggle
     * @return void
     */
    public function testToggleTask(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $taskRepository = static::getContainer()->get(TaskRepository::class);

        $testUser = $userRepository->findOneBy(['email' => "user1@todo.fr"]);
        $client->loginUser($testUser);

        $testTask = $testUser->getTasks()[0];
        $this->assertEquals(false, $testTask->isDone());

        $client->request('GET', '/tasks/'.$testTask->getId().'/toggle');

        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $testTask = $taskRepository->findOneBy(['id' => $testTask->getId()]);

        $this->assertEquals(true, $testTask->isDone());
    }

    /**
     * Test task delete with permission (authenticated user is the task's author)
     * @return void
     */
    public function testDeleteTaskAllowed(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $taskRepository = static::getContainer()->get(TaskRepository::class);

        $testUser = $userRepository->findOneBy(['email' => "user2@todo.fr"]);
        $client->loginUser($testUser);

        $testTask = $taskRepository->findOneBy(['title' => "Tache user 2"]);
        $client->request('GET', '/tasks/'.$testTask->getId().'/delete');

        $this->assertResponseStatusCodeSame(302);
        $this->assertNull($taskRepository->find(3));
    }

    /**
     * Test task delete with no permission (authenticated user is not the task's author)
     * @return void
     */
    public function testDeleteTaskNotAllowed(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $taskRepository = static::getContainer()->get(TaskRepository::class);

        $testUser = $userRepository->findOneBy(['email' => "user1@todo.fr"]);
        $client->loginUser($testUser);

        $testTask = $taskRepository->findOneBy(['title' => "Tache user 2"]);
        $client->request('GET', '/tasks/'.$testTask->getId().'/delete');

        $this->assertNotEquals($testUser->getId(), $testTask->getAuthor()->getId());
        $this->assertResponseStatusCodeSame(403);
    }


}
