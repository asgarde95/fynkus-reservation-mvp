<?php

namespace App\Tests\Infrastructure\Repository;

use App\Domain\Model\Booking;
use App\Domain\Model\CommonSpace;
use App\Infrastructure\Repository\DoctrineBookingRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class DoctrineBookingRepositoryTest extends TestCase
{
    public function testSavePersistsAndFlushes()
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $repo = new DoctrineBookingRepository($entityManager);

        $booking = $this->createMock(Booking::class);

        $entityManager->expects($this->once())->method('persist')->with($booking);
        $entityManager->expects($this->once())->method('flush');

        $repo->save($booking);
    }

    public function testFindAvailabilityAllSlotsAvailable()
    {
        $space = new CommonSpace('Gym');
        $date = new DateTimeImmutable('2025-07-07');

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $repo = $this->getMockBuilder(DoctrineBookingRepository::class)
            ->setConstructorArgs([$entityManager])
            ->onlyMethods(['findBySpaceAndDate'])
            ->getMock();

        $repo->expects($this->once())
            ->method('findBySpaceAndDate')
            ->with($space, $date)
            ->willReturn([]); // No bookings: all slots available

        $slots = $repo->findAvailability($space, $date);

        $this->assertCount(12, $slots); // 9:00 to 21:00 => 12 slots
        $this->assertSame('09:00', $slots[0]['time']);
        $this->assertTrue($slots[0]['available']);
        $this->assertSame('20:00', $slots[11]['time']);
    }

    public function testFindAvailabilityWithConflicts()
    {
        $space = new CommonSpace('Pool');
        $date = new DateTimeImmutable('2025-07-07');
        $entityManager = $this->createMock(EntityManagerInterface::class);

        // Mock a booking from 10:00 to 11:00
        $booking = $this->createMock(Booking::class);
        $booking->method('getStartTime')->willReturn(new DateTimeImmutable('2025-07-07 10:00'));
        $booking->method('getEndTime')->willReturn(new DateTimeImmutable('2025-07-07 11:00'));

        $repo = $this->getMockBuilder(DoctrineBookingRepository::class)
            ->setConstructorArgs([$entityManager])
            ->onlyMethods(['findBySpaceAndDate'])
            ->getMock();

        $repo->expects($this->once())
            ->method('findBySpaceAndDate')
            ->with($space, $date)
            ->willReturn([$booking]);

        $slots = $repo->findAvailability($space, $date);

        // Slot 10:00 should not be available
        $slotTimes = array_column($slots, 'time');
        $availability = array_column($slots, 'available', 'time');

        $this->assertFalse($availability['10:00']);
        // Other slots should be available
        $this->assertTrue($availability['09:00']);
        $this->assertTrue($availability['11:00']);
    }

    public function testHasTimeConflictTrueAndFalse()
    {
        $space = new CommonSpace('Padel');
        $date = new DateTimeImmutable('2025-07-07');
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $repoReflection = new \ReflectionClass(DoctrineBookingRepository::class);
        $repo = new DoctrineBookingRepository($entityManager);

        $method = $repoReflection->getMethod('hasTimeConflict');
        $method->setAccessible(true);

        $booking = $this->createMock(Booking::class);
        $booking->method('getStartTime')->willReturn(new DateTimeImmutable('2025-07-07 10:00'));
        $booking->method('getEndTime')->willReturn(new DateTimeImmutable('2025-07-07 11:00'));

        // Overlapping slot
        $result = $method->invoke($repo, new DateTimeImmutable('2025-07-07 10:30'), new DateTimeImmutable('2025-07-07 11:30'), $booking);
        $this->assertTrue($result);

        // Non-overlapping slot
        $result = $method->invoke($repo, new DateTimeImmutable('2025-07-07 11:00'), new DateTimeImmutable('2025-07-07 12:00'), $booking);
        $this->assertFalse($result);
    }
}
