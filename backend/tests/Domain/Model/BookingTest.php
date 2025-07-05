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
    }

    /**
     * @throws Exception
     */
    public function testCannotCreateBookingWithInvalidTimeRange()
    {
        $this->expectException(\InvalidArgumentException::class);

        $space = new CommonSpace('Padel Court');
        $date = new DateTimeImmutable('2023-01-01');
        $startTime = new DateTimeImmutable('2023-01-01 11:00');
        $endTime = new DateTimeImmutable('2023-01-01 10:00');

        new Booking($space, $date, $startTime, $endTime);
    }
}
