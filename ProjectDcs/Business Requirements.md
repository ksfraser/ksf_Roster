# Business Requirements - ksf_Roster

## Project Overview
Shift scheduling and employee availability - integrates with Calendar and Leave.

## Problem Statement
- Need fixed schedules (8-5)
- Need flexible schedules (8 hours between 6am-6pm)
- Need rotating shift schedules
- Need to see employee availability for meeting invites
- Need to catch coverage gaps

## Employee Schedule Types

### Fixed Schedule
- Start/End time same every day
- Example: 8:00 - 17:00

### Flexible Schedule  
- 8 hour day, can vary
- Example: 7:00-15:00 OR 9:00-17:00

### Rotating Shift
- Days/Weeks in sequence
- Example: Day shift (M-W), Night shift (Th-Sat), Off (Sun-Tue)

## Scope

### Shift Templates
- Name (e.g., "Day Shift", "Night Shift")
- Start time
- End time
- Days of week

### Schedule Types
- Fixed
- Flexible  
- Rotating

### Team Assignment
- Assign schedule to teams
- Override for individual employees

### Calendar Integration
- Daily schedule → Calendar "Available" blocks
- Meeting invites check availability
- iCal free/busy integration

### Leave Impact
- Leave request warns if coverage gap
- Flag critical shift positions
- Allow override with approval

### Training Impact
- Training scheduled during shift warns
- Flag training conflicts

## Schedule Fields
- schedule_name
- schedule_type (fixed/flexible/rotating)
- default_start_time
- default_end_time
- rotation_days

## Integration
- ksf_HRM: Employee schedule assignment
- ksf_Leave: Leave approval checks coverage
- ksf_Training: Training checks shift conflicts
- ksf_Calendar: "Available" calendar entries