<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AttendanceSyncRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'data' => 'required|array',
            'data.*.id' => 'required|integer',
            'data.*.emp_code' => 'required|string|max:255',
            'data.*.first_name' => 'required|string|max:255',
            'data.*.last_name' => 'nullable|string|max:255',
            'data.*.department' => 'required|string|max:255',
            'data.*.position' => 'required|string|max:255',
            'data.*.punch_time' => 'required|date_format:Y-m-d H:i:s',
            'data.*.punch_state' => 'required|string|max:1'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()
        ], 422));
    }
}
