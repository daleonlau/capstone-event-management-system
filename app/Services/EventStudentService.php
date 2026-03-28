<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Student;
use App\Models\EventStudent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EventStudentService
{
    /**
     * Get eligible students for an event based on criteria
     */
    public static function getEligibleStudents(Event $event)
    {
        $query = Student::where('user_id', $event->user_id);
        
        // Filter by departments
        if (!empty($event->departments) && is_array($event->departments)) {
            $query->whereIn('department', $event->departments);
        }
        
        // Filter by courses
        if (!empty($event->courses) && is_array($event->courses)) {
            $query->whereIn('course', $event->courses);
        }
        
        // Filter by year levels
        if (!empty($event->year_levels) && is_array($event->year_levels)) {
            $query->whereIn('yearlevel', $event->year_levels);
        }
        
        return $query->get();
    }
    
    /**
     * Check if a student is eligible for an event
     */
    public static function isStudentEligible(Event $event, $studentId): bool
    {
        $student = Student::where('student_id', $studentId)
            ->where('user_id', $event->user_id)
            ->first();
            
        if (!$student) {
            Log::info('Student not found', ['student_id' => $studentId]);
            return false;
        }
        
        Log::info('Checking student eligibility', [
            'student_id' => $studentId,
            'student_department' => $student->department,
            'student_course' => $student->course,
            'student_yearlevel' => $student->yearlevel,
            'event_departments' => $event->departments,
            'event_courses' => $event->courses,
            'event_year_levels' => $event->year_levels,
        ]);
        
        // Check department eligibility
        if (!empty($event->departments) && is_array($event->departments)) {
            if (!in_array($student->department, $event->departments)) {
                Log::info('Department mismatch', [
                    'student_department' => $student->department,
                    'event_departments' => $event->departments
                ]);
                return false;
            }
        }
        
        // Check course eligibility
        if (!empty($event->courses) && is_array($event->courses)) {
            if (!in_array($student->course, $event->courses)) {
                Log::info('Course mismatch', [
                    'student_course' => $student->course,
                    'event_courses' => $event->courses
                ]);
                return false;
            }
        }
        
        // Check year level eligibility
        if (!empty($event->year_levels) && is_array($event->year_levels)) {
            if (!in_array($student->yearlevel, $event->year_levels)) {
                Log::info('Year level mismatch', [
                    'student_yearlevel' => $student->yearlevel,
                    'event_year_levels' => $event->year_levels
                ]);
                return false;
            }
        }
        
        Log::info('Student is eligible', ['student_id' => $studentId]);
        return true;
    }
    
    /**
     * Get event students with eligibility filter applied
     * This returns only students that match the event's criteria
     */
    public static function getFilteredEventStudents(Event $event)
    {
        $query = EventStudent::where('event_id', $event->id)
            ->with('student');
        
        // Filter students by event criteria using whereHas
        $query->whereHas('student', function($q) use ($event) {
            if (!empty($event->departments) && is_array($event->departments)) {
                $q->whereIn('department', $event->departments);
            }
            if (!empty($event->courses) && is_array($event->courses)) {
                $q->whereIn('course', $event->courses);
            }
            if (!empty($event->year_levels) && is_array($event->year_levels)) {
                $q->whereIn('yearlevel', $event->year_levels);
            }
        });
        
        return $query->get();
    }
    
    /**
     * Get count of eligible students for an event
     */
    public static function getEligibleCount(Event $event): int
    {
        return self::getEligibleStudents($event)->count();
    }
    
    /**
     * Sync students - remove ineligible students and add eligible ones
     */
    public static function syncEligibleStudents(Event $event): array
    {
        $stats = [
            'added' => 0,
            'removed' => 0,
            'kept' => 0,
            'total_eligible' => 0,
            'errors' => 0,
        ];
        
        try {
            DB::beginTransaction();
            
            Log::info('Starting sync for event', [
                'event_id' => $event->id,
                'event_name' => $event->event_name,
                'departments' => $event->departments,
                'courses' => $event->courses,
                'year_levels' => $event->year_levels,
            ]);
            
            // Get all students in event_student
            $existingStudents = EventStudent::where('event_id', $event->id)
                ->pluck('student_id')
                ->toArray();
            
            // Get eligible students based on criteria
            $eligibleStudents = self::getEligibleStudents($event);
            $eligibleStudentIds = $eligibleStudents->pluck('student_id')->toArray();
            
            $stats['total_eligible'] = count($eligibleStudentIds);
            
            Log::info('Eligible students found', [
                'count' => $stats['total_eligible'],
                'student_ids' => $eligibleStudentIds
            ]);
            
            // Remove students who are no longer eligible
            $studentsToRemove = array_diff($existingStudents, $eligibleStudentIds);
            foreach ($studentsToRemove as $studentId) {
                EventStudent::where('event_id', $event->id)
                    ->where('student_id', $studentId)
                    ->delete();
                $stats['removed']++;
                Log::info('Removed ineligible student', [
                    'event_id' => $event->id,
                    'student_id' => $studentId
                ]);
            }
            
            // Add new eligible students
            $studentsToAdd = array_diff($eligibleStudentIds, $existingStudents);
            foreach ($studentsToAdd as $studentId) {
                try {
                    EventStudent::create([
                        'event_id' => $event->id,
                        'student_id' => $studentId,
                        'user_id' => $event->user_id,
                        'status' => $event->payment === 'Payment' ? 'Pending' : 'Paid',
                        'amount_paid' => $event->payment === 'Payment' ? 0 : $event->event_fee,
                    ]);
                    $stats['added']++;
                    Log::info('Added eligible student', [
                        'event_id' => $event->id,
                        'student_id' => $studentId
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to add student', [
                        'event_id' => $event->id,
                        'student_id' => $studentId,
                        'error' => $e->getMessage()
                    ]);
                    $stats['errors']++;
                }
            }
            
            $stats['kept'] = count($existingStudents) - $stats['removed'];
            
            DB::commit();
            
            Log::info('Event student sync completed', [
                'event_id' => $event->id,
                'stats' => $stats
            ]);
            
            return $stats;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to sync event students', [
                'event_id' => $event->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}