<?php

declare(strict_types=1);

namespace Ksfraser\Roster\Service;

use Ksfraser\Roster\Entity\Roster;

class RosterService implements RosterServiceInterface
{
    private $db;
    private string $tablePrefix;
    
    public function __construct($db = null, string $tablePrefix = 'fa_')
    {
        $this->db = $db;
        $this->tablePrefix = $tablePrefix;
    }
    
    private function getTable(string $table): string
    {
        return $this->tablePrefix . $table;
    }
    
    private function escape($value): string
    {
        if ($this->db && method_exists($this->db, 'escape')) {
            return $this->db->escape($value);
        }
        return "'" . addslashes($value) . "'";
    }
    
    private function escapeInt($value): int
    {
        return (int)$value;
    }
    
    public function createShift(Roster $roster): int
    {
        if (!$this->db) {
            throw new \RuntimeException('Database connection not set');
        }
        
        $sql = "INSERT INTO " . $this->getTable('roster_shifts') . "
            (employee_id, date, shift, start_time, end_time, status, created_by, created_at, notes)
            VALUES (
                " . $this->escapeInt($roster->getEmployeeId()) . ",
                " . $this->escape($roster->getDate()) . ",
                " . $this->escape($roster->getShift()) . ",
                " . $this->escape($roster->getStartTime() ?? '') . ",
                " . $this->escape($roster->getEndTime() ?? '') . ",
                " . $this->escape($roster->getStatus()) . ",
                " . $this->escapeInt($roster->getCreatedBy()) . ",
                " . $this->escape($roster->getCreatedAt()) . ",
                " . $this->escape($roster->getNotes()) . "
            )";
        
        $this->db->query($sql);
        return $this->db->insert_id;
    }
    
    public function getShift(int $id): ?Roster
    {
        if (!$this->db) {
            throw new \RuntimeException('Database connection not set');
        }
        
        $sql = "SELECT * FROM " . $this->getTable('roster_shifts') . " WHERE id = " . $this->escapeInt($id);
        $result = $this->db->query($sql);
        
        if (!$result || $this->db->num_rows($result) == 0) {
            return null;
        }
        
        return Roster::fromArray($this->db->fetch($result));
    }
    
    public function updateShift(Roster $roster): bool
    {
        if (!$this->db || !$roster->getId()) {
            return false;
        }
        
        $sql = "UPDATE " . $this->getTable('roster_shifts') . " SET
            employee_id = " . $this->escapeInt($roster->getEmployeeId()) . ",
            date = " . $this->escape($roster->getDate()) . ",
            shift = " . $this->escape($roster->getShift()) . ",
            start_time = " . $this->escape($roster->getStartTime() ?? '') . ",
            end_time = " . $this->escape($roster->getEndTime() ?? '') . ",
            status = " . $this->escape($roster->getStatus()) . ",
            notes = " . $this->escape($roster->getNotes()) . "
            WHERE id = " . $this->escapeInt($roster->getId());
        
        return $this->db->query($sql);
    }
    
    public function deleteShift(int $id): bool
    {
        if (!$this->db) {
            return false;
        }
        
        $sql = "DELETE FROM " . $this->getTable('roster_shifts') . " WHERE id = " . $this->escapeInt($id);
        return $this->db->query($sql);
    }
    
    public function listShifts(array $filters = []): array
    {
        if (!$this->db) {
            throw new \RuntimeException('Database connection not set');
        }
        
        $sql = "SELECT * FROM " . $this->getTable('roster_shifts') . " WHERE 1=1";
        
        if (!empty($filters['employee_id'])) {
            $sql .= " AND employee_id = " . $this->escapeInt($filters['employee_id']);
        }
        if (!empty($filters['date'])) {
            $sql .= " AND date = " . $this->escape($filters['date']);
        }
        if (!empty($filters['start_date'])) {
            $sql .= " AND date >= " . $this->escape($filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $sql .= " AND date <= " . $this->escape($filters['end_date']);
        }
        if (!empty($filters['status'])) {
            $sql .= " AND status = " . $this->escape($filters['status']);
        }
        
        $sql .= " ORDER BY date, start_time";
        
        $result = $this->db->query($sql);
        $shifts = [];
        
        while ($row = $this->db->fetch($result)) {
            $shifts[] = Roster::fromArray($row);
        }
        
        return $shifts;
    }
    
    public function getShiftsByEmployee(int $employeeId, string $startDate, string $endDate): array
    {
        return $this->listShifts([
            'employee_id' => $employeeId,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);
    }
    
    public function getShiftsByDate(string $date): array
    {
        return $this->listShifts(['date' => $date]);
    }
    
    public function getWeeklyRoster(string $weekStart): array
    {
        $endDate = date('Y-m-d', strtotime($weekStart . ' +6 days'));
        return $this->listShifts([
            'start_date' => $weekStart,
            'end_date' => $endDate,
        ]);
    }
    
    public function assignShift(int $rosterId, int $employeeId): bool
    {
        if (!$this->db) {
            return false;
        }
        
        $sql = "UPDATE " . $this->getTable('roster_shifts') . " SET
            employee_id = " . $this->escapeInt($employeeId) . "
            WHERE id = " . $this->escapeInt($rosterId);
        
        return $this->db->query($sql);
    }
    
    public function swapShifts(int $rosterId1, int $rosterId2): bool
    {
        if (!$this->db) {
            return false;
        }
        
        $shift1 = $this->getShift($rosterId1);
        $shift2 = $this->getShift($rosterId2);
        
        if (!$shift1 || !$shift2) {
            return false;
        }
        
        $employeeId1 = $shift1->getEmployeeId();
        $employeeId2 = $shift2->getEmployeeId();
        
        $sql1 = "UPDATE " . $this->getTable('roster_shifts') . " SET employee_id = " . $this->escapeInt($employeeId2) . " WHERE id = " . $this->escapeInt($rosterId1);
        $sql2 = "UPDATE " . $this->getTable('roster_shifts') . " SET employee_id = " . $this->escapeInt($employeeId1) . " WHERE id = " . $this->escapeInt($rosterId2);
        
        return $this->db->query($sql1) && $this->db->query($sql2);
    }
    
    public function getAvailability(int $employeeId, string $date): array
    {
        if (!$this->db) {
            return [];
        }
        
        $sql = "SELECT * FROM " . $this->getTable('roster_availability') . "
            WHERE employee_id = " . $this->escapeInt($employeeId) . "
            AND date = " . $this->escape($date) . "
            AND status = 'Available'";
        
        $result = $this->db->query($sql);
        $availability = [];
        
        while ($row = $this->db->fetch($result)) {
            $availability[] = $row;
        }
        
        return $availability;
    }
    
    public function getPublishedSchedule(): ?string
    {
        if (!$this->db) {
            return null;
        }
        
        $sql = "SELECT value FROM " . $this->getTable('sys_prefs') . " WHERE name = 'roster_published_week'";
        $result = $this->db->query($sql);
        
        if ($result && $this->db->num_rows($result) > 0) {
            $row = $this->db->fetch($result);
            return $row['value'];
        }
        
        return null;
    }
    
    public function publishSchedule(string $weekStart): bool
    {
        if (!$this->db) {
            return false;
        }
        
        $sql = "INSERT INTO " . $this->getTable('sys_prefs') . " (name, value) VALUES ('roster_published_week', " . $this->escape($weekStart) . ")
            ON DUPLICATE KEY UPDATE value = " . $this->escape($weekStart);
        
        return $this->db->query($sql);
    }
}