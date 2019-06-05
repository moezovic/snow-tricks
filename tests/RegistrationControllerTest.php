<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{

    public function testEmptyPassword()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');
        $form = $crawler->selectButton('Envoyer')->form();

        $form['user[nickName]'] = 'JoJo';
        $form['user[email]'] = 'jojo@jojo.fr';
        $form['user[plainPassword][first]'] = '';
        $form['user[plainPassword][second]'] = '';

        $crawler = $client->submit($form);

        $this->assertEquals(
          1, $crawler->filter('body:contains("La valeur ne peut pas être vide")')->count()
        );

    }

    /**
     * @dataProvider passProvider
     */
    public function testPasswordRegex($pass)
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');
        $form = $crawler->selectButton('Envoyer')->form();

        $form['user[nickName]'] = 'JoJo';
        $form['user[email]'] = 'jojo@jojo.fr';
        $form['user[plainPassword][first]'] = $pass;
        $form['user[plainPassword][second]'] = $pass;

        $crawler = $client->submit($form);

        $this->assertEquals(
          1, $crawler->filter('body:contains("Votre mot de passe doit contenir: un chiffre, un majuscule, un minuscule")')->count()
        );

    }

    public function passProvider(){
      return [
        ['lemotdepassedejojo'],
        ['jojo1234']
      ];
    }

    public function testValidPassword()
    {
      $client = static::createClient();
      $crawler = $client->request('GET', '/register');
      $form = $crawler->selectButton('Envoyer')->form();

      $form['user[nickName]'] = 'JoJo';
      $form['user[email]'] = 'jojo@jojo.fr';
      $form['user[plainPassword][first]'] = 'JoJo1234';
      $form['user[plainPassword][second]'] = 'JoJo1234';

      $crawler = $client->submit($form);

      $this->assertTrue(
          $client->getResponse()->isRedirect('/login')
        );
    }

    

    public function testUniqueEmail()
    {
      $client = static::createClient();
      $crawler = $client->request('GET', '/register');
      $form = $crawler->selectButton('Envoyer')->form();

      $form['user[nickName]'] = 'admin';
      $form['user[email]'] = 'admin@admin.com';
      $form['user[plainPassword][first]'] = 'Admin1234';
      $form['user[plainPassword][second]'] = 'Admin1234';

      $crawler = $client->submit($form);

      $this->assertEquals(
          1, $crawler->filter('body:contains("Cet Email est deja utilisé")')->count()
        );
    }

}
