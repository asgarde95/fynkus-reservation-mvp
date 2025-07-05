<?php

namespace App\Domain\Repository;

use App\Domain\Model\Booking;
use App\Domain\Model\CommonSpace;

interface BookingRepositoryInterface
{
    public function findAvailability(CommonSpace $space, \DateTimeInterface $date): array;

    public function save(Booking $booking): void;

    public function findBySpaceAndDate(CommonSpace $space, \DateTimeInterface $date): array;
}
