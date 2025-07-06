<?php

namespace App\Tests\Application\Service;

use App\Application\Service\BookingService;
use App\Domain\Model\Booking;
use App\Domain\Model\CommonSpace;
use App\Domain\Repository\BookingRepositoryInterface;
use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Exception;

class BookingServiceTest extends TestCase
{
    /** @var BookingRepositoryInterface|MockObject */
    private $bookingRepository;

    /** @var BookingService */
    private $service;

    protected function setUp(): void
    {
        $this->bookingRepository = $this->createMock(BookingRepositoryInterface::class);
        $this->service = new BookingService($this->bookingRepository);
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testCheckAvailabilityReturnsRepositoryResult()
    {
        $space = $this->createMock(CommonSpace::class);
        $date = new \DateTimeImmutable('2025-07-07');
        $expected = ['foo'];

        $this->bookingRepository->expects($this->once())
            ->method('findAvailability')
            ->with($space, $date)
            ->willReturn($expected);

        $result = $this->service->checkAvailability($space, $date);
        $this->assertSame($expected, $result);
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testCreateBookingSuccessfully()
    {
        $space = $this->createMock(CommonSpace::class);
        $date = new DateTimeImmutable('2025-07-07');
        $startTime = new DateTimeImmutable('2025-07-07 10:00:00');
        $endTime = new DateTimeImmutable('2025-07-07 11:00:00');

        $this->bookingRepository->expects($this->once())
            ->method('findBySpaceAndDate')
            ->with($space, $date)
            ->willReturn([]);

        $this->bookingRepository->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Booking::class));

        $booking = $this->service->createBooking($space, $date, $startTime, $endTime);

        $this->assertInstanceOf(Booking::class, $booking);
        $this->assertSame($space, $booking->getSpace());
        $this->assertEquals($date, $booking->getDate());
        $this->assertEquals($startTime, $booking->getStartTime());
        $this->assertEquals($endTime, $booking->getEndTime());
    }

    public function testCreateBookingThrowsExceptionOnTimeConflict()
    {
        $space = $this->createMock(CommonSpace::class);
        $date = new DateTimeImmutable('2025-07-07');
        $startTime = new DateTimeImmutable('2025-07-07 10:00:00');
        $endTime = new DateTimeImmutable('2025-07-07 11:00:00');

        $existingBooking = $this->createMock(Booking::class);
        $existingBooking->method('getStartTime')
            ->willReturn(new DateTimeImmutable('2025-07-07 10:30:00'));
        $existingBooking->method('getEndTime')
            ->willReturn(new DateTimeImmutable('2025-07-07 11:30:00'));

        $this->bookingRepository->expects($this->once())
            ->method('findBySpaceAndDate')
            ->with($space, $date)
            ->willReturn([$existingBooking]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Time slot already booked');

        $this->service->createBooking($space, $date, $startTime, $endTime);
    }

    public function testHasTimeConflictReturnsTrueOnOverlap()
    {
        $space = $this->createMock(CommonSpace::class);
        $date = new DateTimeImmutable('2025-07-07');
        $startTime = new DateTimeImmutable('2025-07-07 10:00:00');
        $endTime = new DateTimeImmutable('2025-07-07 11:00:00');

        $existingBooking = $this->createMock(Booking::class);
        $existingBooking->method('getStartTime')
            ->willReturn(new DateTimeImmutable('2025-07-07 10:30:00'));
        $existingBooking->method('getEndTime')
            ->willReturn(new DateTimeImmutable('2025-07-07 11:30:00'));

        // Use reflection to access the private method
        $method = new \ReflectionMethod(BookingService::class, 'hasTimeConflict');
        $method->setAccessible(true);

        $result = $method->invoke(
            $this->service,
            $startTime,
            $endTime,
            $existingBooking
        );

        $this->assertTrue($result);
    }

    public function testHasTimeConflictReturnsFalseWhenNoOverlap()
    {
        $space = $this->createMock(CommonSpace::class);
        $date = new DateTimeImmutable('2025-07-07');
        $startTime = new DateTimeImmutable('2025-07-07 08:00:00');
        $endTime = new DateTimeImmutable('2025-07-07 09:00:00');

        $existingBooking = $this->createMock(Booking::class);
        $existingBooking->method('getStartTime')
            ->willReturn(new DateTimeImmutable('2025-07-07 10:00:00'));
        $existingBooking->method('getEndTime')
            ->willReturn(new DateTimeImmutable('2025-07-07 11:00:00'));

        // Use reflection to access the private method
        $method = new \ReflectionMethod(BookingService::class, 'hasTimeConflict');
        $method->setAccessible(true);

        $result = $method->invoke(
            $this->service,
            $startTime,
            $endTime,
            $existingBooking
        );

        $this->assertFalse($result);
    }
}
