<?php

declare(strict_types=1);

namespace ksfraser\Roster\Entity;

class Roster
{
    public const SHIFT_MORNING = 'Morning';
    public const SHIFT_AFTERNOON = 'Afternoon';
    public const SHIFT_NIGHT = 'Night';
    public const SHIFT_SWING = 'Swing';
    
    private ?int $id = null;
    private int $employeeId = 0;
    private string $date = '';
    private string $shift = '';
    private ?string $startTime = null;
    private ?string $endTime = null;
    private string $status = 'Scheduled';
    private ?int $createdBy = null;
    private string $createdAt = '';
    private string $notes = '';
    
    private static array $scheduledShifts = [];

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): self { $this->id = $id; return $this; }
    public function getEmployeeId(): int { return $this->employeeId; }
    public function setEmployeeId(int $employeeId): self { $this->employeeId = $employeeId; return $this; }
    public function getDate(): string { return $this->date; }
    public function setDate(string $date): self { $this->date = $date; return $this; }
    public function getShift(): string { return $this->shift; }
    public function setShift(string $shift): self { $this->shift = $shift; return $this; }
    public function getStartTime(): ?string { return $this->startTime; }
    public function setStartTime(?string $startTime): self { $this->startTime = $startTime; return $this; }
    public function getEndTime(): ?string { return $this->endTime; }
    public function setEndTime(?string $endTime): self { $this->endTime = $endTime; return $this; }
    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }
    public function getCreatedBy(): ?int { return $this->createdBy; }
    public function setCreatedBy(?int $createdBy): self { $this->createdBy = $createdBy; return $this; }
    public function getCreatedAt(): string { return $this->createdAt; }
    public function setCreatedAt(string $createdAt): self { $this->createdAt = $createdAt; return $this; }
    public function getNotes(): string { return $this->notes; }
    public function setNotes(string $notes): self { $this->notes = $notes; return $this; }
    
    public function isScheduled(): bool { return $this->status === 'Scheduled'; }
    public function isCompleted(): bool { return $this->status === 'Completed'; }
    public function isCancelled(): bool { return $this->status === 'Cancelled'; }
    public function isNoShow(): bool { return $this->status === 'No Show'; }
    
    public function getHours(): float
    {
        if (!$this->startTime || !$this->endTime) {
            return 0.0;
        }
        $start = strtotime($this->startTime);
        $end = strtotime($this->endTime);
        return round(($end - $start) / 3600, 2);
    }
    
    public static function fromArray(array $data): self
    {
        $roster = new self();
        $roster->setId($data['id'] ?? null);
        $roster->setEmployeeId($data['employee_id'] ?? 0);
        $roster->setDate($data['date'] ?? '');
        $roster->setShift($data['shift'] ?? '');
        $roster->setStartTime($data['start_time'] ?? null);
        $roster->setEndTime($data['end_time'] ?? null);
        $roster->setStatus($data['status'] ?? 'Scheduled');
        $roster->setCreatedBy($data['created_by'] ?? null);
        $roster->setCreatedAt($data['created_at'] ?? date('Y-m-d H:i:s'));
        $roster->setNotes($data['notes'] ?? '');
        return $roster;
    }
    
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employeeId,
            'date' => $this->date,
            'shift' => $this->shift,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
            'status' => $this->status,
            'created_by' => $this->createdBy,
            'created_at' => $this->createdAt,
            'notes' => $this->notes,
        ];
    }
}