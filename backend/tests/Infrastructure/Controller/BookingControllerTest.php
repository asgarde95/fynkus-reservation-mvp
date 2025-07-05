<?php

namespace App\Tests\Infrastructure\Controller;

use App\Application\Service\BookingService;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookingControllerTest extends WebTestCase
{
    public function testGetSpaces()
    {
        $client = static::createClient();
        $client->request('GET', '/api/spaces');

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertCount(3, $data);
    }

    /**
     * @throws Exception
     */
    public function testGetAvailability()
    {
        $client = static::createClient();

        // Mock del servicio
        $bookingService = $this->createMock(BookingService::class);
        $bookingService->method('checkAvailability')
            ->willReturn([['time' => '09:00', 'available' => true]]);

        static::getContainer()->set(BookingService::class, $bookingService);

        $client->request('GET', '/api/availability?space=padel&date=2023-01-01');

        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('time', $response[0]);
    }
}
