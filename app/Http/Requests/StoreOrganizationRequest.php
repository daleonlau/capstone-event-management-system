<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrganizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'organization_name' => 'required|string|max:255',
            'organization_email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'assigned_departments' => 'required|array|min:1',
            'assigned_courses' => 'required|array|min:1',
            
            'president_name' => 'required|string|max:255',
            'president_email' => 'required|string|email|max:255|unique:organization_users,email',
            'president_password' => 'required|string|min:6|confirmed',
            
            'adviser_name' => 'required|string|max:255',
            'adviser_email' => 'required|string|email|max:255|unique:organization_users,email',
            'adviser_password' => 'required|string|min:6|confirmed',
            
            'treasurer_name' => 'required|string|max:255',
            'treasurer_email' => 'required|string|email|max:255|unique:organization_users,email',
            'treasurer_password' => 'required|string|min:6|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'assigned_departments.required' => 'Please select at least one department.',
            'assigned_courses.required' => 'Please select at least one course.',
        ];
    }
}