<?php

namespace App\Domain\Model;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Exception;

#[ORM\Entity]
#[ORM\Table(name: 'bookings')]
class Booking
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'UUID')]
    #[ORM\Column(type: 'string', length: 36, unique: true)]
    private ?string $id = null;

    #[ORM\Embedded(class: CommonSpace::class)]
    private CommonSpace $space;

    #[ORM\Column(type: 'date')]
    private DateTimeInterface $date;

    #[ORM\Column(type: 'time', name: 'start_time')]
    private DateTimeInterface $startTime;

    #[ORM\Column(type: 'time', name: 'end_time')]
    private DateTimeInterface $endTime;

    #[ORM\Column(type: 'boolean', name: 'is_active')]
    private bool $isActive;

    /**
     * @throws Exception
     * @throws Exception
     */
    public function __construct(
        CommonSpace $space,
        DateTimeImmutable $date,
        DateTimeImmutable $startTime,
        DateTimeImmutable $endTime,
        bool $isActive = true
    ) {
        $this->space = $space;
        $this->date = $date;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->isActive = $isActive;

        $this->validateTimeRange($startTime, $endTime);
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getSpace(): CommonSpace
    {
        return $this->space;
    }

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    public function getStartTime(): DateTimeInterface
    {
        return $this->startTime;
    }

    public function getEndTime(): DateTimeInterface
    {
        return $this->endTime;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function deactivate(): void
    {
        $this->isActive = false;
    }

    public function activate(): void
    {
        $this->isActive = true;
    }

    public function overlapsWith(DateTimeInterface $start, DateTimeInterface $end): bool
    {
        return $this->startTime < $end && $this->endTime > $start;
    }

    /**
     * @throws Exception
     */
    private function validateTimeRange(DateTimeInterface $start, DateTimeInterface $end): void
    {
        if ($start >= $end) {
            throw new \Exception('End time must be after start time');
        }
    }
}
