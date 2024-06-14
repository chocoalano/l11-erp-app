<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class ProfileUpdateRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'roles' => 'required|array',
            'roles.*' => 'integer|exists:roles,id',
            'email_verified_at' => 'nullable|date',
            'phone' => 'required|string|max:20',
            'placebirth' => 'required|string|max:255',
            'datebirth' => 'required|date',
            'gender' => 'required|in:m,f',
            'blood' => 'required|string|max:3',
            'marital_status' => 'required|string|max:20',
            'religion' => 'required|string|max:50',
            'image' => 'nullable|string|max:255',
            'idtype' => 'required|string|max:50',
            'idnumber' => 'required|string|max:50',
            'ispermanent' => 'required|boolean',
            'idexpired' => 'nullable|date',
            'postalcode' => 'required|string|max:10',
            'citizen_id_address' => 'required|string|max:255',
            'use_as_residential' => 'required|boolean',
            'residential_address' => 'required_if:use_as_residential,false|string|max:255',
            'bank_name' => 'required|string|max:50',
            'bank_account' => 'required|string|max:50',
            'bank_account_holder' => 'required|string|max:50',
            'password' => 'nullable|string|confirmed|min:8',
            'bpjs_ketenagakerjaan' => 'nullable|string|max:50',
            'npp_bpjs_ketenagakerjaan' => 'nullable|string|max:50',
            'bpjs_ketenagakerjaan_date' => 'nullable|date',
            'bpjs_kesehatan' => 'nullable|string|max:50',
            'bpjs_kesehatan_family' => 'nullable|string|max:255',
            'bpjs_kesehatan_date' => 'nullable|date',
            'bpjs_kesehatan_cost' => 'nullable|numeric',
            'jht_cost' => 'nullable|date',
            'jaminan_pensiun_cost' => 'nullable|numeric',
            'jaminan_pensiun_date' => 'nullable|date',
            'organization_id' => 'required|integer|exists:organizations,id',
            'job_position_id' => 'required|integer|exists:job_positions,id',
            'job_level_id' => 'required|integer|exists:job_levels,id',
            'approval_line' => 'required|boolean',
            'approval_manager' => 'required|boolean',
            'company_id' => 'required|integer|exists:companies,id',
            'branch_id' => 'required|integer|exists:branches,id',
            'status' => 'required|string|in:contract,permanent',
            'join_date' => 'required|date',
            'sign_date' => 'required|date',
            'emergency_contact' => 'required|array',
            'emergency_contact.*.id' => 'nullable|integer|exists:u_emergency_contacts,id',
            'emergency_contact.*.user_id' => 'required|integer|exists:users,id',
            'emergency_contact.*.name' => 'required|string|max:255',
            'emergency_contact.*.relationship' => 'required|string|max:50',
            'emergency_contact.*.phone' => 'required|string|max:20',
            'emergency_contact.*.profesion' => 'nullable|string|max:255',
            'family' => 'required|array',
            'family.*.id' => 'nullable|integer|exists:u_families,id',
            'family.*.user_id' => 'required|integer|exists:users,id',
            'family.*.fullname' => 'required|string|max:255',
            'family.*.relationship' => 'required|string|max:50',
            'family.*.birthdate' => 'required|date',
            'family.*.marital_status' => 'required|string|max:20',
            'family.*.job' => 'nullable|string|max:255',
            'formal_education' => 'required|array',
            'formal_education.*.id' => 'nullable|integer|exists:u_formal_education,id',
            'formal_education.*.user_id' => 'required|integer|exists:users,id',
            'formal_education.*.grade_id' => 'required|integer|exists:grades,id',
            'formal_education.*.institution' => 'required|string|max:255',
            'formal_education.*.majors' => 'required|string|max:255',
            'formal_education.*.score' => 'required|numeric',
            'formal_education.*.start' => 'required|date',
            'formal_education.*.finish' => 'required|date',
            'formal_education.*.description' => 'nullable|string|max:255',
            'formal_education.*.certification' => 'required|boolean',
            'formal_education.*.file' => 'nullable|file|mimes:pdf|max:2048',
            'informal_education' => 'nullable|array',
            'informal_education.*.id' => 'nullable|integer|exists:u_informal_education,id',
            'informal_education.*.name' => 'required|string|max:255',
            'informal_education.*.start' => 'required|date',
            'informal_education.*.finish' => 'required|date',
            'informal_education.*.expired' => 'nullable|date',
            'informal_education.*.type' => 'nullable|string|max:50',
            'informal_education.*.duration' => 'required|integer|min:0',
            'informal_education.*.fee' => 'required|numeric|min:0',
            'informal_education.*.certification' => 'required|boolean',
            'informal_education.*.description' => 'nullable|string|max:255',
            'basic_salary' => 'required|numeric|min:0',
            'salary_type' => 'required|string|in:Monthly,Weekly,Daily',
            'payment_schedule' => 'nullable|string|max:255',
            'prorate_settings' => 'nullable|string|max:255',
            'overtime_settings' => 'nullable|string|max:255',
            'cost_center' => 'nullable|string|max:255',
            'cost_center_category' => 'nullable|string|max:255',
            'currency' => 'required|string|max:3',
            'npwp_15_digit_old' => 'nullable|string|size:15',
            'npwp_16_digit_new' => 'nullable|string|size:16',
            'ptkp_status' => 'required|string|max:3',
            'tax_method' => 'required|string|in:gross,net',
            'tax_salary' => 'required|string|in:taxable,nontaxable',
            'emp_tax_status' => 'required|string|in:permanent,contract',
            'beginning_netto' => 'required|numeric|min:0',
            'pph21_paid' => 'required|numeric|min:0',
            'work_experience' => 'nullable|array',
            'work_experience.*.id' => 'nullable|integer|exists:u_work_experiences,id',
            'work_experience.*.company' => 'required|string|max:255',
            'work_experience.*.position' => 'required|string|max:255',
            'work_experience.*.from' => 'required|date',
            'work_experience.*.to' => 'required|date',
            'work_experience.*.length_of_service' => 'required|string|max:255',
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
