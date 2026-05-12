<?php

declare(strict_types=1);

namespace Ksfraser\Roster\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Ksfraser\Roster\Service\RosterService;
use Ksfraser\Roster\Entity\Roster;

class RosterServiceTest extends TestCase
{
    public function testCanCreateRosterService(): void
    {
        $service = new RosterService();
        $this->assertInstanceOf(RosterService::class, $service);
    }

    public function testListShiftsWithoutDbThrowsException(): void
    {
        $service = new RosterService();
        $this->expectException(\RuntimeException::class);
        $service->listShifts();
    }

    public function testCreateShiftWithoutDbThrowsException(): void
    {
        $service = new RosterService();
        $roster = new Roster();
        $roster->setEmployeeId(1);
        $roster->setDate('2026-05-12');
        
        $this->expectException(\RuntimeException::class);
        $service->createShift($roster);
    }

    public function testGetWeeklyRosterWithoutDbThrowsException(): void
    {
        $service = new RosterService();
        $this->expectException(\RuntimeException::class);
        $service->getWeeklyRoster('2026-05-12');
    }

    public function testGetShiftsByEmployeeWithoutDbThrowsException(): void
    {
        $service = new RosterService();
        $this->expectException(\RuntimeException::class);
        $service->getShiftsByEmployee(1, '2026-05-12', '2026-05-18');
    }

    public function testGetShiftsByDateWithoutDbThrowsException(): void
    {
        $service = new RosterService();
        $this->expectException(\RuntimeException::class);
        $service->getShiftsByDate('2026-05-12');
    }

    public function testDeleteShiftWithoutDbReturnsFalse(): void
    {
        $service = new RosterService();
        $result = $service->deleteShift(1);
        $this->assertFalse($result);
    }

    public function testAssignShiftWithoutDbReturnsFalse(): void
    {
        $service = new RosterService();
        $result = $service->assignShift(1, 5);
        $this->assertFalse($result);
    }

    public function testSwapShiftsWithoutDbReturnsFalse(): void
    {
        $service = new RosterService();
        $result = $service->swapShifts(1, 2);
        $this->assertFalse($result);
    }

    public function testUpdateShiftWithoutDbReturnsFalse(): void
    {
        $service = new RosterService();
        $roster = new Roster();
        $result = $service->updateShift($roster);
        $this->assertFalse($result);
    }

    public function testGetShiftWithoutDbThrowsException(): void
    {
        $service = new RosterService();
        $this->expectException(\RuntimeException::class);
        $service->getShift(1);
    }

    public function testGetPublishedScheduleWithoutDbReturnsNull(): void
    {
        $service = new RosterService();
        $result = $service->getPublishedSchedule();
        $this->assertNull($result);
    }

    public function testPublishScheduleWithoutDbReturnsFalse(): void
    {
        $service = new RosterService();
        $result = $service->publishSchedule('2026-05-12');
        $this->assertFalse($result);
    }

    public function testGetAvailabilityWithoutDbReturnsEmptyArray(): void
    {
        $service = new RosterService();
        $result = $service->getAvailability(1, '2026-05-12');
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testServiceAcceptsCustomTablePrefix(): void
    {
        $service = new RosterService(null, 'custom_');
        $this->assertInstanceOf(RosterService::class, $service);
    }

    public function testServiceImplementsInterface(): void
    {
        $service = new RosterService();
        $this->assertInstanceOf(\Ksfraser\Roster\Service\RosterServiceInterface::class, $service);
    }
}