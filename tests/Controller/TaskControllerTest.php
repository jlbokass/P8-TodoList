<?php

namespace App\Tests;

use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 * @coversNothing
 */
class TaskControllerTest extends WebTestCase
{
    /** @var EntityManagerInterface */
    public $entityManager;

    /**
     * @var KernelBrowser
     */
    public $client;

    public function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient([], [
            'PHP_AUTH_USER' => 'j_dabok@me.com',
            'PHP_AUTH_PW' => 'Dabok@1977',
        ]);

        $this->client->disableReboot();

        $this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $this->entityManager->beginTransaction();
        $this->entityManager->getConnection()->setAutoCommit(false);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->rollback();
        $this->entityManager->close();
        $this->entityManager = null;
    }

    /**
     * @dataProvider getSecureUrls
     */
    public function testSecureURL(string $url)
    {
        $client = static::createClient();
        $client->request('GET', $url);
        $this->assertStringContainsString('/login', $client->getResponse()->getTargetUrl());

    }

    public function getSecureUrls()
    {
        yield['/tasks'];
        yield['/tasks/create'];
        yield['/users'];
    }

    public function testPageIsUp()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'j_dabok@me.com',
            'PHP_AUTH_PW' => 'Dabok@1977',
        ]);
        $crawler = $this->client->request('GET', '/tasks');
        $this->assertGreaterThanOrEqual(
            1,
            $crawler->filter('h1')->count(),
            'Liste des tâches'
        );
    }

    public function testNumberOfTaskOnPage()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'j_dabok@me.com',
            'PHP_AUTH_PW' => 'Dabok@1977',
        ]);
        $crawler = $this->client->request('GET', '/tasks');

        $this->assertCount(4, $crawler->filter('div.card'));
        $this->assertGreaterThan(
            0,
            $crawler->filter( 'html:contains("Vous avez 4 tâches.")' )->count()
        );
    }

    public function testCreateNewTask()
    {
        $crawler = $this->client->request('GET', '/tasks/create');
        $form = $crawler->selectButton('Ajoutez')->form([
            'task[name]' => 'From unit test',
            'task[content]' => 'Juste some new content',
        ]);
        $this->client->submit($form);

        /** @var Task $task */
        $task = $this->entityManager->getRepository(Task::class)->findOneBy([
            'name' => 'From unit test',
        ]);
        self::assertSame(302, $this->client->getResponse()->getStatusCode());
        //$this->assertNotNull($task);
        $this->assertSame('From unit test', $task->getName());
        $this->assertSame('Juste some new content', $task->getContent());
    }

    public function testEditTask()
    {
        $crawler = $this->client->request('GET', '/task/54/edit');
        $form = $crawler->selectButton('Modifiez')->form([
            'task[name]' => 'Edit from unit test',
        ]);

        $this->client->submit($form);
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());

        /** @var Task $task */
        $task = $this->entityManager->getRepository(Task::class)->find(54);
        $this->assertSame('Edit from unit test', $task->getName());
    }

    public function testDeleteTask()
    {
        $crawler = $this->client->request('GET', '/task/50/delete');

        /** @var Task $task */
        $task = $this->entityManager->getRepository(Task::class)->find(50);
        $this->assertNull($task);
    }
}
