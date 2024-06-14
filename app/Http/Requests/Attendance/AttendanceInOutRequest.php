<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AttendanceInOutRequest extends FormRequest
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
            'flag' => 'required|string|in:in,out',
            'nik' => 'required|string|max:20',
            'schedule_group_attendances_id' => 'required|integer|exists:schedule_group_attendances,id',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i:s',
            'photo' => 'nullable|image|max:2048',
            'location' => 'nullable|string|max:255',
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
