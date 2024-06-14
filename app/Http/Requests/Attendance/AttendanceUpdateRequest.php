<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AttendanceUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'in.flag' => 'required|string|in:in',
            'in.nik' => 'required|string|size:8',
            'in.schedule_group_attendances_id' => 'required|integer|exists:schedule_group_attendances,id',
            'in.lat' => 'required|numeric',
            'in.lng' => 'required|numeric',
            'in.date' => 'required|date_format:Y-m-d',
            'in.time' => 'required|date_format:H:i:s',
            'out.flag' => 'required|string|in:out',
            'out.nik' => 'required|string|size:8',
            'out.schedule_group_attendances_id' => 'required|integer|exists:schedule_group_attendances,id',
            'out.lat' => 'required|numeric',
            'out.lng' => 'required|numeric',
            'out.date' => 'required|date_format:Y-m-d',
            'out.time' => 'required|date_format:H:i:s',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()
        ], 422));
    }
}
