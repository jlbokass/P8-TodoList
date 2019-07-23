<?php

namespace App\Tests;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegisterControllerTest extends WebTestCase
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

    public function testCreateNewUser()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/registration');

        $form = $crawler->selectButton('S\'inscrire')->form([
            'registration[username]' => 'username1',
            'registration[email]' => 'test5@gmail.com',
            'registration[password][first]' => 'test1234',
            'registration[password][second]' => 'test1234',
        ]);

        $this->client->submit($form);

        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'email' => 'test5@gmail.com',
        ]);
        self::assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertNotNull($user);
        $this->assertSame('username1', $user->getUsername());
    }
}
