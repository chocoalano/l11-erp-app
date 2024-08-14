<?php

namespace App\Filament\Resources\UserManagementResource\Pages;

use App\Filament\Resources\UserManagementResource;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Models\UserRelated\UEmergencyContact;
use App\Models\UserRelated\UFamily;
use App\Models\UserRelated\UFormalEducation;
use App\Models\UserRelated\UInformalEducation;
use App\Models\UserRelated\UWorkExperience;
use Exception;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;

class CreateUserManagement extends CreateRecord
{
    protected static string $resource = UserManagementResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if(!is_null($data['password'])){
            $data['password'] = bcrypt($data['password']);
        }
        return $data;
    }
    protected function handleRecordCreation(array $data): Model
    {   
        DB::beginTransaction();
        try {
            $u = new User([
                'name' => $data['name'],
                'nik' => $data['nik'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'phone' => $data['phone'],
                'placebirth' => $data['placebirth'],
                'datebirth' => $data['datebirth'],
                'gender' => $data['gender'],
                'blood' => $data['blood'],
                'marital_status' => $data['marital_status'],
                'religion' => $data['religion'],
                'image' => $data['image'],
            ]);
            $u->save();
            $role = Role::whereIn('id', $data['roles'])->get();

            $u->assignRole($role);
            $u->address()->create([
                'idtype' => $data['idtype'] ?? 'ktp',
                'idnumber' => $data['idnumber'],
                'idexpired' => $data['idexpired'],
                'ispermanent' => $data['ispermanent'],
                'postalcode' => $data['postalcode'],
                'citizen_id_address' => $data['citizen_id_address'],
                'use_as_residential' => $data['use_as_residential'],
                'residential_address' => $data['residential_address'],
            ]);

            $u->bank()->create([
                'bank_name' => $data['bank_name'],
                'bank_account' => $data['bank_account'],
                'bank_account_holder' => $data['bank_account_holder'],
            ]);

            $u->bpjs()->create([
                'bpjs_ketenagakerjaan'=>$data['bpjs_ketenagakerjaan'],
                'npp_bpjs_ketenagakerjaan'=>$data['npp_bpjs_ketenagakerjaan'],
                'bpjs_ketenagakerjaan_date'=>$data['bpjs_ketenagakerjaan_date'],
                'bpjs_kesehatan'=>$data['bpjs_kesehatan'],
                'bpjs_kesehatan_family'=>$data['bpjs_kesehatan_family'],
                'bpjs_kesehatan_date'=>$data['bpjs_kesehatan_date'],
                'bpjs_kesehatan_cost'=>$data['bpjs_kesehatan_cost'] ?? 0,
                'jht_cost'=>$data['jht_cost'],
                'jaminan_pensiun_cost'=>$data['jaminan_pensiun_cost'],
                'jaminan_pensiun_date'=>$data['jaminan_pensiun_date'],
            ]);
            
            $emergency_contact = [];
            foreach ($data['emergency_contact'] as $k) {
                if(!empty($k['name']) || !is_null($k['name'])){
                    $emergency_contact[] = new UEmergencyContact($k);
                }
            }
            $family = [];
            foreach ($data['family'] as $k) {
                if(!empty($k['fullname']) || !is_null($k['fullname'])){
                    $family[] = new UFamily($k);
                }
            }
            $formal_education = [];
            foreach ($data['formal_education'] as $k) {
                if(!empty($k['grade_id']) || !is_null($k['grade_id'])){
                    $formal_education[] = new UFormalEducation($k);
                }
            }
            $informal_education = [];
            foreach ($data['informal_education'] as $k) {
                if(!empty($k['name']) || !is_null($k['name'])){
                    $informal_education[] = new UInformalEducation($k);
                }
            }
            $work_experience = [];
            foreach ($data['work_experience'] as $k) {
                if(!empty($k['company']) || !is_null($k['company'])){
                    $work_experience[] = new UWorkExperience($k);
                }
            }
            if(count($formal_education) > 0){
                $u->formal_education()->saveMany($formal_education);
            }
            if(count($emergency_contact) > 0){
                $u->emergency_contact()->saveMany($emergency_contact);
            }
            if(count($family) > 0){
                $u->family()->saveMany($family);
            }
            if(count($informal_education) > 0){
                $u->informal_education()->saveMany($informal_education);
            }
            if(count($work_experience) > 0){
                $u->work_experience()->saveMany($work_experience);
            }

            $u->employe()->create([
                'organization_id'=>$data['organization_id'],
                'job_position_id'=>$data['job_position_id'],
                'job_level_id'=>$data['job_level_id'],
                'approval_line'=>$data['approval_line'],
                'approval_manager'=>$data['approval_manager'],
                'company_id'=>$data['company_id'],
                'branch_id'=>$data['branch_id'],
                'status'=>$data['status'],
                'join_date'=>$data['join_date'],
                'sign_date'=>$data['sign_date'],
            ]);

            $u->salary()->create([
                'basic_salary'=>$data['basic_salary'] ?? 0,
                'salary_type'=>$data['salary_type'] ?? 'Monthly',
                'payment_schedule'=>$data['payment_schedule'],
                'prorate_settings'=>$data['prorate_settings'],
                'overtime_settings'=>$data['overtime_settings'],
                'cost_center'=>$data['cost_center'],
                'cost_center_category'=>$data['cost_center_category'],
                'currency'=>$data['currency'],
            ]);

            $u->tax_config()->create([
                'npwp_15_digit_old'=>$data['npwp_15_digit_old'],
                'npwp_16_digit_new'=>$data['npwp_16_digit_new'],
                'ptkp_status'=>$data['ptkp_status'],
                'tax_method'=>$data['tax_method'],
                'tax_salary'=>$data['tax_salary'],
                'emp_tax_status'=>$data['emp_tax_status'],
                'beginning_netto'=>$data['beginning_netto'] ?? 0,
                'pph21_paid'=>$data['pph21_paid'] ?? 0,
            ]);
            
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            dd($e->getMessage());
            Notification::make()->title($e)->danger()->send();
        }
        return $u;
    }
}
