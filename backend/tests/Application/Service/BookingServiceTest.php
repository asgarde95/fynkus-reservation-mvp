<?php

namespace App\Tests\Application\Service;

use App\Application\Service\BookingService;
use App\Domain\Model\CommonSpace;
use App\Domain\Repository\BookingRepositoryInterface;
use DateTimeImmutable;
use Exception;
use PHPUnit\Framework\TestCase;

class BookingServiceTest extends TestCase
{
    private BookingService $bookingService;
    private $bookingRepositoryMock;

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    protected function setUp(): void
    {
        $this->bookingRepositoryMock = $this->createMock(BookingRepositoryInterface::class);
        $this->bookingService = new BookingService($this->bookingRepositoryMock);
    }

    public function testCheckAvailability()
    {
        $space = new CommonSpace('Padel Court');
        $date = new DateTimeImmutable('2023-01-01');

        $this->bookingRepositoryMock->method('findBySpaceAndDate')
            ->willReturn([]);

        $availability = $this->bookingService->checkAvailability($space, $date);

        $this->assertCount(12, $availability); // 9-21 = 12 slots
        $this->assertTrue($availability[0]['available']); // Primer slot disponible
    }

    /**
     * @throws Exception
     */
    public function testCreateBookingSuccessfully()
    {
        $space = new CommonSpace('Padel Court');
        $date = new DateTimeImmutable('2023-01-01');
        $startTime = new DateTimeImmutable('2023-01-01 10:00');
        $endTime = new DateTimeImmutable('2023-01-01 11:00');

        $this->bookingRepositoryMock->method('findBySpaceAndDate')
            ->willReturn([]);

        $this->bookingRepositoryMock->expects($this->once())
            ->method('save');

        $booking = $this->bookingService->createBooking($space, $date, $startTime, $endTime);

        $this->assertEquals('Padel Court', $booking->getSpace()->getName());
    }
}
