<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProfileControllerTest extends WebTestCase
{
    public function testPathToProfile()
    {
        $client = static::createClient();
        $client->request('GET', '/profile');

        $this->assertResponseIsSuccessful();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        echo $client->getResponse()->getContent();
    }
}
