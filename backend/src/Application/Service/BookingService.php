<?php

namespace App\Application\Service;

use App\Domain\Model\Booking;
use App\Domain\Model\CommonSpace;
use App\Domain\Repository\BookingRepositoryInterface;
use DateTimeInterface;
use Exception;

class BookingService
{
    public function __construct(
        private BookingRepositoryInterface $bookingRepository
    ) {}

    public function checkAvailability(
        CommonSpace $space,
        DateTimeInterface $date
    ): array {
        return $this->bookingRepository->findAvailability($space, $date);
    }


    /**
     * @throws Exception
     */
    public function createBooking(
        CommonSpace $space,
        \DateTimeImmutable $date,
        \DateTimeImmutable $startTime,
        \DateTimeImmutable $endTime
    ): Booking {
        // Verificar disponibilidad
        $existingBookings = $this->bookingRepository->findBySpaceAndDate($space, $date);

        foreach ($existingBookings as $existing) {
            if ($this->hasTimeConflict($startTime, $endTime, $existing)) {
                throw new \Exception('Time slot already booked');
            }
        }

        $booking = new Booking($space, $date, $startTime, $endTime);
        $this->bookingRepository->save($booking);

        return $booking;
    }

    private function hasTimeConflict(
        DateTimeInterface $newStart,
        DateTimeInterface $newEnd,
        Booking $existing
    ): bool {
        return $newStart < $existing->getEndTime() && $newEnd > $existing->getStartTime();
    }
}
