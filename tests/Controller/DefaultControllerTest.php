<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    /**
     * @var KernelBrowser
     */
    public $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
    }

    public function testClickButtonInscription()
    {

        $crawler = $this->client->request('GET', '/');

        self::assertContains('/registration', $crawler->filter('a')->extract(['href']));
        $link = $crawler->selectLink('Inscription')->link();
        $crawler = $this->client->click($link);

        self::assertContains('Inscription', $crawler->filter('h1')->html());
    }

    public function testClickButtonConnexion()
    {

        $crawler = $this->client->request('GET', '/');

        self::assertContains('/login', $crawler->filter('a')->extract(['href']));
        $link = $crawler->selectLink('Connexion')->link();
        $crawler = $this->client->click($link);

        self::assertContains('Connexion', $crawler->filter('h1')->html());
    }

    public function testTextOnPage()
    {
        $crawler= $this->client->request('GET', '/');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame('BIENVENUE SUR TODO-LIST', $crawler->filter('h1')->text());
    }

    public function clickOnLinkConnexion()
    {
        $crawler = $this->client->request('GET', '/');
        $link = $crawler
            ->filter('a:contains("Connexion")')
            ->link()
            ;

        $crawler->click($link);
    }
}
