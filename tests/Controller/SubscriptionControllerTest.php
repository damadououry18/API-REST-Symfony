<?php

// tests/Controller/SubscriptionControllerTest.php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionControllerTest extends WebTestCase
{
    public function testCreateSubscription(): void
    {
        $client = static::createClient();

        // Données pour l'API
        $data = [
            'contact_id' => 2,
            'product_id' => 1,
            'beginDate' => '2023-02-01',
            'endDate' => '2023-12-31',
        ];

        // Effectuer une requête POST
        $client->request('POST', '/subscription', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));

        // Vérifier la réponse
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);  // Code 201
        $this->assertJsonResponse($client->getResponse());
    }

    private function assertJsonResponse(Response $response, int $statusCode = Response::HTTP_OK)
    {
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
        $this->assertSame($statusCode, $response->getStatusCode());
    }

    public function testUpdateSubscription(): void
    {
        $client = static::createClient();

        // Données pour l'API
        $data = [
            'beginDate' => '2024-01-01',
            'endDate' => '2024-12-31',
        ];

        // Effectuer une requête PUT
        $client->request('PUT', '/subscription/1', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));

        // Vérifier la réponse
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);  // Code 200
        $this->assertJsonResponse($client->getResponse());
    }
    
    public function testDeleteSubscription(): void
    {
        $client = static::createClient();

        // Effectuer une requête DELETE
        $client->request('DELETE', '/subscription/1');

        // Vérifier la réponse
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);  // Code 200
        $this->assertJsonResponse($client->getResponse());
    }


}
