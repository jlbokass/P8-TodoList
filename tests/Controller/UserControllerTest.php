<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testTextOnpage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/users');

        $this->assertSelectorTextContains('h1', 'Manage users');
    }
}
