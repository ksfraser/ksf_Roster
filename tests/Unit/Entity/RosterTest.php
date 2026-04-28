<?php

declare(strict_types=1);

namespace Ksfraser\Roster\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use Ksfraser\Roster\Entity\Roster;

class RosterTest extends TestCase
{
    public function testCanCreateRoster(): void
    {
        $roster = new Roster();
        $this->assertInstanceOf(Roster::class, $roster);
    }

    public function testCanSetAndGetEmployeeId(): void
    {
        $roster = new Roster();
        $roster->setEmployeeId(5);
        $this->assertEquals(5, $roster->getEmployeeId());
    }

    public function testCanSetAndGetDate(): void
    {
        $roster = new Roster();
        $roster->setDate('2024-01-15');
        $this->assertEquals('2024-01-15', $roster->getDate());
    }

    public function testCanSetShift(): void
    {
        $roster = new Roster();
        $roster->setShift(Roster::SHIFT_MORNING);
        $this->assertEquals('Morning', $roster->getShift());
    }

    public function testCalculatesHours(): void
    {
        $roster = new Roster();
        $roster->setStartTime('09:00');
        $roster->setEndTime('17:00');
        $this->assertEquals(8.0, $roster->getHours());
    }

    public function testFromArray(): void
    {
        $data = [
            'id' => 1,
            'employee_id' => 5,
            'date' => '2024-01-15',
            'shift' => 'Morning',
            'start_time' => '09:00',
            'end_time' => '17:00',
            'status' => 'Scheduled',
            'created_by' => 1,
            'created_at' => '2024-01-10 10:00:00',
            'notes' => 'Test shift',
        ];
        
        $roster = Roster::fromArray($data);
        
        $this->assertEquals(1, $roster->getId());
        $this->assertEquals(5, $roster->getEmployeeId());
        $this->assertEquals('2024-01-15', $roster->getDate());
        $this->assertEquals('Morning', $roster->getShift());
    }

    public function testToArray(): void
    {
        $roster = new Roster();
        $roster->setId(1);
        $roster->setEmployeeId(5);
        $roster->setDate('2024-01-15');
        $roster->setShift(Roster::SHIFT_AFTERNOON);
        $roster->setStatus('Completed');
        
        $arr = $roster->toArray();
        
        $this->assertEquals(1, $arr['id']);
        $this->assertEquals(5, $arr['employee_id']);
        $this->assertEquals('2024-01-15', $arr['date']);
        $this->assertEquals('Afternoon', $arr['shift']);
    }

    public function testIsScheduled(): void
    {
        $roster = new Roster();
        $roster->setStatus('Scheduled');
        $this->assertTrue($roster->isScheduled());
    }

    public function testIsCancelled(): void
    {
        $roster = new Roster();
        $roster->setStatus('Cancelled');
        $this->assertTrue($roster->isCancelled());
    }
}