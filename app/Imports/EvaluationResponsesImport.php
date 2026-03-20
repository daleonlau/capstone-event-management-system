<?php

namespace App\Imports;

use App\Models\EvaluationResponse;
use App\Models\EventStudent;
use App\Models\Student;
use App\Models\Evaluation;
use App\Models\EvaluationQuestion;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class EvaluationResponsesImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, SkipsEmptyRows
{
    protected $evaluation;
    protected $eventId;
    protected $successCount = 0;
    protected $errorCount = 0;
    protected $errors = [];
    protected $likertQuestions = [];
    protected $commentQuestions = [];
    protected $questionIds = [];
    protected $expectedHeaders = [];

    public function __construct(Evaluation $evaluation)
    {
        $this->evaluation = $evaluation;
        $this->eventId = $evaluation->event_id;
        
        // Get all questions for this evaluation
        $this->likertQuestions = $evaluation->questions()
            ->where('question_type', 'likert')
            ->orderBy('order')
            ->get();
        
        $this->commentQuestions = $evaluation->questions()
            ->where('question_type', 'comment')
            ->orderBy('order')
            ->get();
        
        // Store question IDs in order
        foreach ($this->likertQuestions as $q) {
            $this->questionIds[] = $q->id;
        }
        foreach ($this->commentQuestions as $q) {
            $this->questionIds[] = $q->id;
        }
        
        // Build expected headers for reference
        $this->expectedHeaders = [
            'student_id', 'email', 'name', 'age', 'sex', 'agency_office', 'position',
            'respondent_type', 'title_prefix', 'department', 'course', 'year_level'
        ];
        
        $formType = $evaluation->form_type;
        if (in_array($formType, ['type1', 'type3', 'type4'])) {
            $this->expectedHeaders[] = 'speaker_topic';
            $this->expectedHeaders[] = 'speaker_name';
        }
        
        foreach ($this->likertQuestions as $q) {
            $this->expectedHeaders[] = $q->question_text;
        }
        
        foreach ($this->commentQuestions as $q) {
            $this->expectedHeaders[] = $q->question_text;
        }
    }

    /**
     * Map the row data by column position
     */
    public function prepareForValidation($data, $index)
{
    // Skip if student_id is empty or starts with #
    if (empty($data['student_id']) || str_starts_with($data['student_id'], '#')) {
        return [];
    }
    
    $mappedData = [];
    
    // Map standard fields by position (they are the first 12 columns)
    $standardFields = ['student_id', 'email', 'name', 'age', 'sex', 'agency_office', 
                       'position', 'respondent_type', 'title_prefix', 'department', 'course', 'year_level'];
    
    // Get all column values in order
    $columns = array_values($data);
    
    // Map by position
    foreach ($standardFields as $pos => $field) {
        if (isset($columns[$pos])) {
            $value = $columns[$pos];
            
            // Special handling for age - convert to string
            if ($field === 'age' && is_numeric($value)) {
                $mappedData[$field] = (string) $value;
            } else {
                $mappedData[$field] = $value;
            }
        }
    }
    
    // Map speaker fields (next 2 columns if applicable)
    $formType = $this->evaluation->form_type;
    $speakerOffset = 12;
    if (in_array($formType, ['type1', 'type3', 'type4'])) {
        if (isset($columns[$speakerOffset])) {
            $mappedData['speaker_topic'] = $columns[$speakerOffset];
        }
        if (isset($columns[$speakerOffset + 1])) {
            $mappedData['speaker_name'] = $columns[$speakerOffset + 1];
        }
        $speakerOffset += 2;
    }
    
    // Map likert questions by position
    $likertOffset = $speakerOffset;
    foreach ($this->likertQuestions as $pos => $question) {
        if (isset($columns[$likertOffset + $pos])) {
            $value = $columns[$likertOffset + $pos];
            $cleanedValue = $this->cleanNumericValue($value);
            $mappedData["q_{$question->id}"] = $cleanedValue;
        }
    }
    
    // Map comment questions by position
    $commentOffset = $likertOffset + count($this->likertQuestions);
    foreach ($this->commentQuestions as $pos => $question) {
        if (isset($columns[$commentOffset + $pos])) {
            $mappedData["c_{$question->id}"] = $columns[$commentOffset + $pos];
        }
    }
    
    return $mappedData;
}
    /**
     * Clean numeric values - extract numbers from strings
     */
    private function cleanNumericValue($value)
    {
        if (is_null($value) || $value === '') {
            return null;
        }
        
        // If it's already numeric
        if (is_numeric($value)) {
            return (int) $value;
        }
        
        // If it's a string, try to extract number
        if (is_string($value)) {
            $trimmed = trim($value);
            if (is_numeric($trimmed)) {
                return (int) $trimmed;
            }
            // Try to extract first number using regex
            preg_match('/\d+/', $trimmed, $matches);
            if (!empty($matches)) {
                return (int) $matches[0];
            }
        }
        
        return null;
    }

    public function rules(): array
    {
        $rules = [
            'student_id' => ['required', 'string'],
            'email' => ['required', 'email'],
            'name' => ['nullable', 'string'],
            'age' => ['nullable'],  // Accept any type, we'll convert to string
            'sex' => ['nullable', 'string'],
            'agency_office' => ['nullable', 'string'],
            'position' => ['nullable', 'string'],
            'respondent_type' => ['nullable', 'string'],
            'title_prefix' => ['nullable', 'string'],
            'department' => ['required', 'string'],
            'course' => ['required', 'string'],
            'year_level' => ['required', 'string', Rule::in(['1st Year', '2nd Year', '3rd Year', '4th Year'])],
        ];
    
        // Add speaker fields for forms that have speaker
        $formType = $this->evaluation->form_type;
        if (in_array($formType, ['type1', 'type3', 'type4'])) {
            $rules['speaker_topic'] = ['nullable', 'string'];
            $rules['speaker_name'] = ['nullable', 'string'];
        }
    
        // Add likert questions rules
        foreach ($this->likertQuestions as $question) {
            $rules["q_{$question->id}"] = ['required', 'integer', 'min:1', 'max:5'];
        }
    
        // Add comment questions rules (optional)
        foreach ($this->commentQuestions as $question) {
            $rules["c_{$question->id}"] = ['nullable', 'string'];
        }
    
        return $rules;
    }

    public function customValidationMessages()
    {
        $messages = [
            'student_id.required' => 'Student ID is required',
            'email.required' => 'Email is required',
            'email.email' => 'Please enter a valid email address',
            'department.required' => 'Department is required',
            'course.required' => 'Course is required',
            'year_level.required' => 'Year Level is required',
            'year_level.in' => 'Year Level must be 1st Year, 2nd Year, 3rd Year, or 4th Year',
        ];
        
        // Add custom messages for each question
        foreach ($this->likertQuestions as $question) {
            $messages["q_{$question->id}.required"] = "The question '{$question->question_text}' is required";
            $messages["q_{$question->id}.integer"] = "Rating for '{$question->question_text}' must be a number (1-5)";
            $messages["q_{$question->id}.min"] = "Rating for '{$question->question_text}' must be at least 1";
            $messages["q_{$question->id}.max"] = "Rating for '{$question->question_text}' cannot exceed 5";
        }
        
        return $messages;
    }

    public function model(array $row)
{
    // Skip if student_id is empty
    if (empty($row['student_id'])) {
        return null;
    }

    // Check if student exists
    $student = Student::where('student_id', $row['student_id'])
        ->where('user_id', $this->evaluation->organization_id)
        ->first();

    if (!$student) {
        $this->errors[] = "Row with Student ID {$row['student_id']} not found in students table";
        $this->errorCount++;
        return null;
    }

    // Check if already submitted
    $existing = EvaluationResponse::where('evaluation_id', $this->evaluation->id)
        ->where('student_id', $row['student_id'])
        ->exists();

    if ($existing) {
        $this->errors[] = "Student {$row['student_id']} already submitted";
        $this->errorCount++;
        return null;
    }

    // Build likert responses
    $likertResponses = [];
    foreach ($this->likertQuestions as $question) {
        $key = "q_{$question->id}";
        if (isset($row[$key]) && is_numeric($row[$key])) {
            $likertResponses[$question->id] = (int) $row[$key];
        }
    }

    // Build comment responses
    $commentResponses = [];
    foreach ($this->commentQuestions as $question) {
        $key = "c_{$question->id}";
        if (isset($row[$key]) && !empty($row[$key]) && !str_starts_with($row[$key], '#')) {
            $commentResponses[$question->id] = $row[$key];
        }
    }

    // Ensure student is in event_student
    EventStudent::firstOrCreate(
        [
            'event_id' => $this->eventId,
            'student_id' => $row['student_id']
        ],
        [
            'user_id' => $this->evaluation->organization_id,
            'status' => 'Pending',
            'amount_paid' => 0
        ]
    );

    $this->successCount++;

    return new EvaluationResponse([
        'evaluation_id' => $this->evaluation->id,
        'event_id' => $this->eventId,
        'student_id' => $row['student_id'],
        'email' => $row['email'],
        'name' => $row['name'] ?? ($student->firstname . ' ' . $student->lastname),
        'age' => $row['age'] !== null && $row['age'] !== '' ? (string) $row['age'] : null,
        'sex' => $row['sex'] ?? null,
        'agency_office' => $row['agency_office'] ?? null,
        'position' => $row['position'] ?? null,
        'respondent_type' => $row['respondent_type'] ?? null,
        'title_prefix' => $row['title_prefix'] ?? null,
        'department' => $row['department'],
        'course' => $row['course'],
        'year_level' => $row['year_level'],
        'speaker_topic' => $row['speaker_topic'] ?? null,
        'speaker_name' => $row['speaker_name'] ?? null,
        'likert_responses' => $likertResponses,
        'comment_responses' => $commentResponses,
    ]);
}

    public function headingRow(): int
    {
        return 1;
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function getStats(): array
    {
        return [
            'success' => $this->successCount,
            'errors' => $this->errorCount,
            'error_details' => $this->errors
        ];
    }
}