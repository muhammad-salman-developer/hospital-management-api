<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DoctorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('doctor')?->user_id;

        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password' => $this->isMethod('post')
            ? 'required|string|min:8'
            : 'nullable|string|min:8',
            'phone' => 'nullable|string|max:20',

            'department_id' => 'required|exists:departments,id',
            'qualification' => 'required|string|max:255',
            'specialization' => 'required|string|max:255',
            'consultation_fee' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',   // max 2MB
            'status' => 'nullable|in:active,inactive,on_leave',
        ];
    }
}
