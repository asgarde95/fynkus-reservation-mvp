<?php

namespace App\Infrastructure\Repository;

use App\Domain\Model\Booking;
use App\Domain\Model\CommonSpace;
use App\Domain\Repository\BookingRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineBookingRepository implements BookingRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function findAvailability(CommonSpace $space, \DateTimeInterface $date): array
    {
        $bookings = $this->findBySpaceAndDate($space, $date);

        // Generar slots de 9:00 a 21:00
        $slots = [];
        $startHour = 9;
        $endHour = 21;

        for ($hour = $startHour; $hour < $endHour; $hour++) {
            $slotStart = (clone $date)->setTime($hour, 0);
            $slotEnd = (clone $date)->setTime($hour + 1, 0);

            $isAvailable = true;
            foreach ($bookings as $booking) {
                if ($this->hasTimeConflict($slotStart, $slotEnd, $booking)) {
                    $isAvailable = false;
                    break;
                }
            }

            $slots[] = [
                'time' => $slotStart->format('H:i'),
                'available' => $isAvailable
            ];
        }

        return $slots;
    }

    public function save(Booking $booking): void
    {
        $this->entityManager->persist($booking);
        $this->entityManager->flush();
    }

    public function findBySpaceAndDate(CommonSpace $space, \DateTimeInterface $date): array
    {
        return $this->entityManager
            ->getRepository(Booking::class)
            ->createQueryBuilder('b')
            ->where('b.space.name = :spaceName')
            ->andWhere('b.date = :date')
            ->setParameter('spaceName', $space->getName())
            ->setParameter('date', $date->format('Y-m-d'))
            ->getQuery()
            ->getResult();
    }

    private function hasTimeConflict(
        \DateTimeInterface $newStart,
        \DateTimeInterface $newEnd,
        Booking $existing
    ): bool {
        return $newStart < $existing->getEndTime() && $newEnd > $existing->getStartTime();
    }
}
