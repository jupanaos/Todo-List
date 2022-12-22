<?php

namespace App\DataFixtures;

use App\Entity\Task;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TaskFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $task = new Task();
        $task->setTitle('Tache admin');
        $task->setContent('Contenu de la tache admin');
        $task->setCreatedAt(new DateTime());
        $task->toggle(false);
        $task->setAuthor($this->getReference(UserFixtures::ADMIN_REFERENCE));
        $manager->persist($task);

        $task = new Task();
        $task->setTitle('Tache user 1');
        $task->setContent('Contenu de la tache user 1');
        $task->setCreatedAt(new DateTime());
        $task->toggle(false);
        $task->setAuthor($this->getReference(UserFixtures::USER1_REFERENCE));
        $manager->persist($task);

        $task = new Task();
        $task->setTitle('Tache user 2');
        $task->setContent('Contenu de la tache user 2');
        $task->setCreatedAt(new DateTime());
        $task->toggle(false);
        $task->setAuthor($this->getReference(UserFixtures::USER2_REFERENCE));
        $manager->persist($task);

        $task = new Task();
        $task->setTitle('Tache user 2 bis');
        $task->setContent('Contenu de la tache user 2 bis');
        $task->setCreatedAt(new DateTime());
        $task->toggle(true);
        $task->setAuthor($this->getReference(UserFixtures::USER2_REFERENCE));
        $manager->persist($task);

        $task = new Task();
        $task->setTitle('Tache anonyme');
        $task->setContent('Contenu de la tache anonyme');
        $task->setCreatedAt(new DateTime());
        $task->toggle(true);
        $task->setAuthor($this->getReference(UserFixtures::ANON_REFERENCE));
        $manager->persist($task);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
