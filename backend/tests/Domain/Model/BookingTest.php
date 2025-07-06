<?php

namespace App\Tests\Domain\Model;

use App\Domain\Model\Booking;
use App\Domain\Model\CommonSpace;
use DateTimeImmutable;
use Exception;
use PHPUnit\Framework\TestCase;

class BookingTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testCanCreateValidBooking()
    {
        $space = new CommonSpace('Padel Court');
        $date = new DateTimeImmutable('2023-01-01');
        $startTime = new DateTimeImmutable('2023-01-01 10:00');
        $endTime = new DateTimeImmutable('2023-01-01 11:00');

        $booking = new Booking($space, $date, $startTime, $endTime);

        $this->assertEquals('Padel Court', $booking->getSpace()->getName());
        $this->assertEquals('10:00', $booking->getStartTime()->format('H:i'));
        $this->assertEquals('11:00', $booking->getEndTime()->format('H:i'));
        $this->assertTrue($booking->isActive());
        $this->assertNull($booking->getId());
        $this->assertSame($space, $booking->getSpace());
        $this->assertSame($date, $booking->getDate());
        $this->assertSame($startTime, $booking->getStartTime());
        $this->assertSame($endTime, $booking->getEndTime());
    }

    /**
     * @throws Exception
     */
    public function testCannotCreateBookingWithInvalidTimeRange()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('End time must be after start time');

        $space = new CommonSpace('Padel Court');
        $date = new DateTimeImmutable('2023-01-01');
        $startTime = new DateTimeImmutable('2023-01-01 11:00');
        $endTime = new DateTimeImmutable('2023-01-01 10:00');

        new Booking($space, $date, $startTime, $endTime);
    }

    /**
     * @throws Exception
     */
    public function testBookingWithCustomIsActive()
    {
        $space = new CommonSpace('Pool');
        $date = new DateTimeImmutable('2023-02-02');
        $startTime = new DateTimeImmutable('2023-02-02 08:00');
        $endTime = new DateTimeImmutable('2023-02-02 09:00');

        $booking = new Booking($space, $date, $startTime, $endTime, false);

        $this->assertFalse($booking->isActive());
    }

    /**
     * @throws Exception
     */
    public function testGettersReturnCorrectTypesAndValues()
    {
        $space = new CommonSpace('Gym');
        $date = new DateTimeImmutable('2024-05-05');
        $startTime = new DateTimeImmutable('2024-05-05 12:30');
        $endTime = new DateTimeImmutable('2024-05-05 13:30');

        $booking = new Booking($space, $date, $startTime, $endTime);

        $this->assertInstanceOf(CommonSpace::class, $booking->getSpace());
        $this->assertInstanceOf(\DateTimeInterface::class, $booking->getDate());
        $this->assertInstanceOf(\DateTimeInterface::class, $booking->getStartTime());
        $this->assertInstanceOf(\DateTimeInterface::class, $booking->getEndTime());
    }

    /**
     * @throws Exception
     */
    public function testCannotCreateBookingWithEqualStartAndEndTime()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('End time must be after start time');

        $space = new CommonSpace('Padel Court');
        $date = new DateTimeImmutable('2023-01-01');
        $startTime = new DateTimeImmutable('2023-01-01 10:00');
        $endTime = new DateTimeImmutable('2023-01-01 10:00');

        new Booking($space, $date, $startTime, $endTime);
    }
}
