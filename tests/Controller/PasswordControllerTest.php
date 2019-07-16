<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PasswordControllerTest extends WebTestCase
{
    public function testRequestNewPassword()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/reset');

        $form = $crawler->selectButton('Envoyez')->form();
        $form['email_to_reset_password[email]'] = 'test@gmail.com';
        $client->submit($form);

        $client->followRedirect();

        echo $client->getResponse()->getContent();
    }
}
