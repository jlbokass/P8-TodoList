<?php

namespace App\Tests;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class TaskControllerTest extends WebTestCase
{
    /**
     * @var KernelBrowser
     */
    public $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
    }

    /*public function testCreateTask()
    {
        $this->logIn('user', 'password');
        $crawler = $this->client->request('GET', '/tasks/create');

        $this->assertContains('/tasks/create', $crawler->filter('a')->extract(['href']));

        $link = $crawler->selectLink('Ajouter')->link();
        $crawler = $this->client->click($link);

        $this->assertSame(2, $crawler->filter('input')->count());
        $this->assertSame(1, $crawler->filter('html:contains("Annulez")')->count());

        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[name]'] = 'Ma tâche';
        $form['task[content]'] = 'Le contenu de la tâche';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertSame(1, $crawler->filter('html:contains("La tâche a été bien été ajoutée")')->count());
    }*/

    public function logIn($userRole, $password)
    {
        $session = $this->client->getContainer()->get('session');

        $user = $this->getContainer()->get('doctrine')->getRepository(User::class)->findOneBy(['email' => $userRole]);

        $token = new UsernamePasswordToken($user, $password, 'main', ['ROLE_'. strtoupper($userRole)]);

        $session->set('_security_'. 'main', serialize($token));
        $session->save();
        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    private function getContainer()
    {
        self::bootKernel();
        return self::$container;
    }
}
