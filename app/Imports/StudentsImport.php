<?php

namespace App\Imports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation
{
    protected $userId;
    protected $allowedCourses;
    protected $allowedDepartments;

    public function __construct($userId, $allowedCourses = [], $allowedDepartments = [])
    {
        $this->userId = $userId;
        $this->allowedCourses = $allowedCourses;
        $this->allowedDepartments = $allowedDepartments;
    }

    public function model(array $row)
    {
        return new Student([
            'student_id' => $row['student_id'],
            'firstname' => $row['firstname'],
            'lastname' => $row['lastname'],
            'email' => $row['email'],
            'course' => $row['course'],
            'department' => $row['department'],
            'yearlevel' => $row['yearlevel'],
            'user_id' => $this->userId,
        ]);
    }

    public function rules(): array
    {
        return [
            'student_id' => 'required|string',
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|email',
            'course' => [
                'required',
                'string',
                Rule::in($this->allowedCourses)
            ],
            'department' => [
                'required',
                'string',
                Rule::in($this->allowedDepartments)
            ],
            'yearlevel' => 'required|in:1st Year,2nd Year,3rd Year,4th Year',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'course.in' => 'Course ":input" is not assigned to your organization.',
            'department.in' => 'Department ":input" is not assigned to your organization.',
            'yearlevel.in' => 'Year level must be 1st Year, 2nd Year, 3rd Year, or 4th Year.',
        ];
    }
}