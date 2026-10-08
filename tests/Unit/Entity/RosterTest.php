<?php

declare(strict_types=1);

namespace ksfraser\Roster\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use ksfraser\Roster\Entity\Roster;

class RosterTest extends TestCase
{
    public function testCanCreateRoster(): void
    {
        $roster = new Roster();
        $this->assertInstanceOf(Roster::class, $roster);
    }

    public function testCanSetAndGetId(): void
    {
        $roster = new Roster();
        $roster->setId(10);
        $this->assertEquals(10, $roster->getId());
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
        $roster->setDate('2026-05-12');
        $this->assertEquals('2026-05-12', $roster->getDate());
    }

    public function testCanSetAndGetShift(): void
    {
        $roster = new Roster();
        $roster->setShift(Roster::SHIFT_MORNING);
        $this->assertEquals('Morning', $roster->getShift());
    }

    public function testCanSetAndGetStartTime(): void
    {
        $roster = new Roster();
        $roster->setStartTime('09:00:00');
        $this->assertEquals('09:00:00', $roster->getStartTime());
    }

    public function testCanSetAndGetEndTime(): void
    {
        $roster = new Roster();
        $roster->setEndTime('17:30:00');
        $this->assertEquals('17:30:00', $roster->getEndTime());
    }

    public function testCanSetAndGetStatus(): void
    {
        $roster = new Roster();
        $this->assertEquals('Scheduled', $roster->getStatus());
        $roster->setStatus('Completed');
        $this->assertEquals('Completed', $roster->getStatus());
    }

    public function testCanSetAndGetCreatedBy(): void
    {
        $roster = new Roster();
        $roster->setCreatedBy(15);
        $this->assertEquals(15, $roster->getCreatedBy());
    }

    public function testCanSetAndGetCreatedAt(): void
    {
        $roster = new Roster();
        $roster->setCreatedAt('2026-05-01 10:00:00');
        $this->assertEquals('2026-05-01 10:00:00', $roster->getCreatedAt());
    }

    public function testCanSetAndGetNotes(): void
    {
        $roster = new Roster();
        $roster->setNotes('Special shift requirements');
        $this->assertEquals('Special shift requirements', $roster->getNotes());
    }

    public function testShiftConstants(): void
    {
        $this->assertEquals('Morning', Roster::SHIFT_MORNING);
        $this->assertEquals('Afternoon', Roster::SHIFT_AFTERNOON);
        $this->assertEquals('Night', Roster::SHIFT_NIGHT);
        $this->assertEquals('Swing', Roster::SHIFT_SWING);
    }

    public function testCalculatesHoursWithFullTimes(): void
    {
        $roster = new Roster();
        $roster->setStartTime('09:00:00');
        $roster->setEndTime('17:00:00');
        $this->assertEquals(8.0, $roster->getHours());
    }

    public function testCalculatesHoursWithPartialShift(): void
    {
        $roster = new Roster();
        $roster->setStartTime('14:00:00');
        $roster->setEndTime('22:00:00');
        $this->assertEquals(8.0, $roster->getHours());
    }

    public function testCalculatesHoursWithOvertime(): void
    {
        $roster = new Roster();
        $roster->setStartTime('08:00:00');
        $roster->setEndTime('20:00:00');
        $this->assertEquals(12.0, $roster->getHours());
    }

    public function testGetHoursReturnsZeroWhenNoTimesSet(): void
    {
        $roster = new Roster();
        $this->assertEquals(0.0, $roster->getHours());
    }

    public function testGetHoursReturnsZeroWhenOnlyStartTimeSet(): void
    {
        $roster = new Roster();
        $roster->setStartTime('09:00:00');
        $this->assertEquals(0.0, $roster->getHours());
    }

    public function testGetHoursReturnsZeroWhenOnlyEndTimeSet(): void
    {
        $roster = new Roster();
        $roster->setEndTime('17:00:00');
        $this->assertEquals(0.0, $roster->getHours());
    }

    public function testIsScheduled(): void
    {
        $roster = new Roster();
        $roster->setStatus('Scheduled');
        $this->assertTrue($roster->isScheduled());
        $this->assertFalse($roster->isCompleted());
    }

    public function testIsCompleted(): void
    {
        $roster = new Roster();
        $roster->setStatus('Completed');
        $this->assertTrue($roster->isCompleted());
        $this->assertFalse($roster->isScheduled());
    }

    public function testIsCancelled(): void
    {
        $roster = new Roster();
        $roster->setStatus('Cancelled');
        $this->assertTrue($roster->isCancelled());
    }

    public function testIsNoShow(): void
    {
        $roster = new Roster();
        $roster->setStatus('No Show');
        $this->assertTrue($roster->isNoShow());
    }

    public function testFromArray(): void
    {
        $data = [
            'id' => 1,
            'employee_id' => 5,
            'date' => '2026-05-12',
            'shift' => 'Morning',
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'status' => 'Scheduled',
            'created_by' => 1,
            'created_at' => '2026-05-01 10:00:00',
            'notes' => 'Test shift',
        ];
        
        $roster = Roster::fromArray($data);
        
        $this->assertEquals(1, $roster->getId());
        $this->assertEquals(5, $roster->getEmployeeId());
        $this->assertEquals('2026-05-12', $roster->getDate());
        $this->assertEquals('Morning', $roster->getShift());
        $this->assertEquals('09:00:00', $roster->getStartTime());
        $this->assertEquals('17:00:00', $roster->getEndTime());
        $this->assertEquals('Scheduled', $roster->getStatus());
        $this->assertEquals(1, $roster->getCreatedBy());
        $this->assertEquals('Test shift', $roster->getNotes());
    }

    public function testFromArrayWithDefaults(): void
    {
        $data = [
            'employee_id' => 10,
            'date' => '2026-06-01',
        ];
        
        $roster = Roster::fromArray($data);
        
        $this->assertEquals(10, $roster->getEmployeeId());
        $this->assertEquals('2026-06-01', $roster->getDate());
        $this->assertEquals('', $roster->getShift());
        $this->assertEquals('Scheduled', $roster->getStatus());
    }

    public function testToArray(): void
    {
        $roster = new Roster();
        $roster->setId(1);
        $roster->setEmployeeId(5);
        $roster->setDate('2026-05-12');
        $roster->setShift(Roster::SHIFT_AFTERNOON);
        $roster->setStatus('Completed');
        $roster->setNotes('Completed early');
        
        $arr = $roster->toArray();
        
        $this->assertEquals(1, $arr['id']);
        $this->assertEquals(5, $arr['employee_id']);
        $this->assertEquals('2026-05-12', $arr['date']);
        $this->assertEquals('Afternoon', $arr['shift']);
        $this->assertEquals('Completed', $arr['status']);
        $this->assertEquals('Completed early', $arr['notes']);
    }

    public function testFluentInterface(): void
    {
        $roster = (new Roster())
            ->setEmployeeId(10)
            ->setDate('2026-07-15')
            ->setShift(Roster::SHIFT_NIGHT)
            ->setStartTime('22:00:00')
            ->setEndTime('06:00:00')
            ->setStatus('Scheduled')
            ->setNotes('Night shift');

        $this->assertEquals(10, $roster->getEmployeeId());
        $this->assertEquals('2026-07-15', $roster->getDate());
        $this->assertEquals('Night', $roster->getShift());
        $this->assertEquals('Scheduled', $roster->getStatus());
        $this->assertEquals('Night shift', $roster->getNotes());
    }
}