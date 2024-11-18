<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SubscriptionControllerTest extends WebTestCase
{
    public function testGetSubscriptionByContact(): void
    {
        $client = static::createClient();
        $client->request('GET', '/subscription/1'); // Remplacez "1" par un ID valide

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testCreateSubscription(): void
    {
        $client = static::createClient();

        $client->request( 
            'POST',
            '/subscription',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'contact' => 1, // Remplacez avec un ID de contact valide
                'product' => 1, // Remplacez avec un ID de produit valide
                'beginDate' => '2024-01-01',
                'endDate' => '2024-12-31',
            ])
        );

        $this->assertResponseStatusCodeSame(201);
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testUpdateSubscription(): void
    {
        $client = static::createClient();

        $client->request(
            'PUT',
            '/subscription/1', // Remplacez "1" par un ID de subscription valide
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'beginDate' => '2024-02-01',
                'endDate' => '2024-11-30',
            ])
        );

        $this->assertResponseStatusCodeSame(200);
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testDeleteSubscription(): void
    {
        $client = static::createClient();

        $client->request('DELETE', '/subscription/1'); // Remplacez "1" par un ID de subscription valide

        $this->assertResponseStatusCodeSame(204);
    }
    
}
