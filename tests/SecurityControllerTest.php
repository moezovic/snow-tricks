<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testValidCredentials()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Connexion')->form();

        $form['_username'] = 'admin@admin.com';
        $form['_password'] = 'pass_1234';

        $crawler = $client->submit($form);
        $crawler = $client->followRedirect();
       
        $this->assertEquals(
          1, $crawler->filter('li:contains("Nouvelle figure")')->count()
        );
    }

    public function testWrongCredentials()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Connexion')->form();

        $form['_username'] = 'wrong@wrong.com';
        $form['_password'] = 'mkgfg87';

        $crawler = $client->submit($form);
        $crawler = $client->followRedirect();
       
        $this->assertEquals(
          1, $crawler->filter('span:contains("Identifiants invalides.")')->count()
        );
    }
}
