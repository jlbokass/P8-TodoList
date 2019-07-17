<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    private $client;

    public function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    public function testTextOnPage()
    {
        $crawler= $this->client->request('GET', '/');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame('BIENVENUE SUR TODO-LIST', $crawler->filter('h1')->text());
    }
}
