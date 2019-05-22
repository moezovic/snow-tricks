<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TrickControllerTest extends WebTestCase
{
    public function testIndexHomePage()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'admin@admin.com',
            'PHP_AUTH_PW'   => 'pass_1234',
        ]);
        $crawler = $client->request('GET', '/');

        $this->assertGreaterThan(0, $crawler->filter('.fa-arrow-down')->count());
    }

    public function testNewTrick()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'admin@admin.com',
            'PHP_AUTH_PW'   => 'pass_1234',
        ]);
        
        $crawler = $client->request('GET', '/member/new');

        

        $form = $crawler->selectButton('Valider')->form();


        $form['trick[name]'] = 'Trick 88';
        $form['trick[description]'] = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla condimentum ipsum ut eleifend sollicitudin. Praesent euismod nulla id faucibus condimentum. Aliquam eget enim ornare, faucibus libero pellentesque, vehicula nunc. Vestibulum interdum dignissim viverra. Integer nisi purus, consectetur vestibulum iaculis et, egestas ac lorem. Ut sollicitudin mauris pellentesque, commodo magna in, interdum quam. Quisque venenatis auctor nibh vel venenatis.';

        $form['trick[niveau]'] = 1;

        $form['trick[trick_group]'] = 1;

        // TO COMPLETE : Test insertion of new file//

        // $form['trick[imgDocs][7]'] = 'canadian-bacon.jpg';

        $crawler = $client->submit($form);
        $this->assertTrue(
          $client->getResponse()->isRedirect('/')
        );
    }

    public function testShowTrick()
    {

    }
}








