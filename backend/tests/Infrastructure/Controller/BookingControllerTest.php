<?php

namespace App\Tests\Infrastructure\Controller;

use App\Application\Service\BookingService;
use App\Domain\Model\Booking;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookingControllerTest extends WebTestCase
{
    public function testGetSpaces()
    {
        $client = static::createClient();
        $client->request('GET', '/api/api/spaces');

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertCount(3, $data);
        $this->assertEquals(['id' => 'padel', 'name' => 'Padel Court'], $data[0]);
    }

    /**
     * @throws Exception
     */
    public function testGetAvailability()
    {
        $client = static::createClient();

        $bookingService = $this->createMock(BookingService::class);
        $bookingService->method('checkAvailability')
            ->willReturn([['time' => '09:00', 'available' => true]]);

        static::getContainer()->set(BookingService::class, $bookingService);

        $client->request('GET', '/api/api/availability?space=padel&date=2025-01-01');

        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($response);
        $this->assertArrayHasKey('time', $response[0]);
        $this->assertArrayHasKey('available', $response[0]);
    }

    public function testGetAvailabilityWithInvalidDateReturnsError()
    {
        $client = static::createClient();

        // No mocking, let the controller throw on invalid date
        $client->request('GET', '/api/api/availability?space=padel&date=invalid-date');

        $this->assertResponseStatusCodeSame(400);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $response);
    }

    public function testCreateBookingSuccess()
    {
        $client = static::createClient();

        $bookingService = $this->createMock(BookingService::class);
        $booking = $this->createMock(Booking::class);
        $booking->method('getId')->willReturn('bk-123');

        $bookingService->method('createBooking')->willReturn($booking);
        static::getContainer()->set(BookingService::class, $bookingService);

        $payload = [
            'space' => 'gym',
            'date' => '2025-07-07',
            'time' => '09:00'
        ];

        $client->request(
            'POST',
            '/api/api/bookings',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $this->assertResponseStatusCodeSame(201);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('success', $response['status']);
        $this->assertEquals('bk-123', $response['bookingId']);
    }

    public function testCreateBookingReturnsErrorOnException()
    {
        $client = static::createClient();

        $bookingService = $this->createMock(BookingService::class);
        $bookingService->method('createBooking')
            ->willThrowException(new \Exception('Time slot already booked'));

        static::getContainer()->set(BookingService::class, $bookingService);

        $payload = [
            'space' => 'gym',
            'date' => '2025-07-07',
            'time' => '09:00'
        ];

        $client->request(
            'POST',
            '/api/api/bookings',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $this->assertResponseStatusCodeSame(400);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('error', $response['status']);
        $this->assertEquals('Time slot already booked', $response['message']);
    }
}
