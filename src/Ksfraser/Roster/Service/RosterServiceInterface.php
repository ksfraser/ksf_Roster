<?php

declare(strict_types=1);

namespace Ksfraser\Roster\Service;

use Ksfraser\Roster\Entity\Roster;

interface RosterServiceInterface
{
    public function createShift(Roster $roster): int;
    public function getShift(int $id): ?Roster;
    public function updateShift(Roster $roster): bool;
    public function deleteShift(int $id): bool;
    public function listShifts(array $filters = []): array;
    public function getShiftsByEmployee(int $employeeId, string $startDate, string $endDate): array;
    public function getShiftsByDate(string $date): array;
    public function getWeeklyRoster(string $weekStart): array;
    public function assignShift(int $rosterId, int $employeeId): bool;
    public function swapShifts(int $rosterId1, int $rosterId2): bool;
    public function getAvailability(int $employeeId, string $date): array;
    public function getPublishedSchedule(): ?string;
    public function publishSchedule(string $weekStart): bool;
}