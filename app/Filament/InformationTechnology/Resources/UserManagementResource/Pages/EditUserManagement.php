<?php

namespace App\Filament\InformationTechnology\Resources\UserManagementResource\Pages;

use App\Filament\InformationTechnology\Resources\UserManagementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;

class EditUserManagement extends EditRecord
{
    protected static string $resource = UserManagementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $u = User::with(
            'address',
            'bank',
            'bpjs',
            'emergency_contact',
            'family',
            'formal_education',
            'informal_education',
            'work_experience',
            'employe',
            'salary',
            'tax_config'
            )->find($data['id']);
        $showdata=[
            "name" => $u->name,
            "nik" => $u->nik,
            "email" => $u->email,
            "email_verified_at" => $u->email_verified_at,
            "phone" => $u->phone,
            "placebirth" => $u->placebirth,
            "datebirth" => $u->datebirth,
            "gender" => $u->gender,
            "blood" => $u->blood,
            "marital_status" => $u->marital_status,
            "religion" => $u->religion,
            "image" => $u->image,
            "password" => null,
            "password_confirmation" => null,
            "emergency_contact" => $u->emergency_contact,
            "family" => $u->family,
            "formal_education" => $u->formal_education,
            "informal_education" => $u->informal_education,
            "work_experience" => $u->work_experience
        ];
        if(!is_null($u->address)){
            $showdata["idtype"] = $u->address->idtype;
            $showdata["idnumber"] = $u->address->idnumber;
            $showdata["idexpired"] = $u->address->idexpired;
            $showdata["postalcode"] = $u->address->postalcode;
            $showdata["ispermanent"] = $u->address->ispermanent;
            $showdata["use_as_residential"] = $u->address->use_as_residential;
            $showdata["citizen_id_address"] = $u->address->citizen_id_address;
            $showdata["residential_address"] = $u->address->residential_address;
        }if(!is_null($u->bank)){
            $showdata["bank_name"] = $u->bank->bank_name;
            $showdata["bank_account"] = $u->bank->bank_account;
            $showdata["bank_account_holder"] = $u->bank->bank_account_holder;
        }if(!is_null($u->salary)){
            $showdata["basic_salary"] = $u->salary->basic_salary;
            $showdata["salary_type"] = $u->salary->salary_type;
            $showdata["payment_schedule"] = $u->salary->payment_schedule;
            $showdata["prorate_settings"] = $u->salary->prorate_settings;
            $showdata["overtime_settings"] = $u->salary->overtime_settings;
            $showdata["cost_center"] = $u->salary->cost_center;
            $showdata["cost_center_category"] = $u->salary->cost_center_category;
            $showdata["currency"] = $u->salary->currency;
        }if(!is_null($u->employe)){
            $showdata["organization_id"] = $u->employe->organization_id;
            $showdata["job_position_id"] = $u->employe->job_position_id;
            $showdata["job_level_id"] = $u->employe->job_level_id;
            $showdata["approval_line"] = $u->employe->approval_line;
            $showdata["approval_manager"] = $u->employe->approval_manager;
            $showdata["branch_id"] = $u->employe->branch_id;
            $showdata["status"] = $u->employe->status;
            $showdata["join_date"] = $u->employe->join_date;
            $showdata["sign_date"] = $u->employe->sign_date;
        }if(!is_null($u->bpjs)){
            $showdata["bpjs_ketenagakerjaan"] = $u->bpjs->bpjs_ketenagakerjaan;
            $showdata["npp_bpjs_ketenagakerjaan"] = $u->bpjs->npp_bpjs_ketenagakerjaan;
            $showdata["bpjs_ketenagakerjaan_date"] = $u->bpjs->bpjs_ketenagakerjaan_date;
            $showdata["bpjs_kesehatan"] = $u->bpjs->bpjs_kesehatan;
            $showdata["bpjs_kesehatan_family"] = $u->bpjs->bpjs_kesehatan_family;
            $showdata["bpjs_kesehatan_date"] = $u->bpjs->bpjs_kesehatan_date;
            $showdata["bpjs_kesehatan_cost"] = $u->bpjs->bpjs_kesehatan_cost;
            $showdata["jht_cost"] = $u->bpjs->jht_cost;
            $showdata["jaminan_pensiun_cost"] = $u->bpjs->jaminan_pensiun_cost;
            $showdata["jaminan_pensiun_date"] = $u->bpjs->jaminan_pensiun_date;
        }
        if(!is_null($u->tax_config)){
            $showdata["npwp_15_digit_old"] = $u->tax_config->npwp_15_digit_old;
            $showdata["npwp_16_digit_new"] = $u->tax_config->npwp_16_digit_new;
            $showdata["ptkp_status"] = $u->tax_config->ptkp_status;
            $showdata["tax_method"] = $u->tax_config->tax_method;
            $showdata["tax_salary"] = $u->tax_config->tax_salary;
            $showdata["emp_tax_status"] = $u->tax_config->emp_tax_status;
            $showdata["beginning_netto"] = $u->tax_config->beginning_netto;
            $showdata["pph21_paid"] = $u->tax_config->pph21_paid;
        }
        return $showdata;
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if(!is_null($data['password'])){
            if($data['password'] === $data['password_confirmation']){
                $data['password'] = bcrypt($data['password']);
            }
        }
        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        DB::beginTransaction();
        try {
            $u = User::find($record->id);
            $u->name = $data['name'];
            $u->nik = $data['nik'];
            $u->email = $data['email'];
            if(!is_null($data['password'])){
                $u->password = bcrypt($data['password']);
            }
            $u->email_verified_at=$data['email_verified_at'];
            $u->phone=$data['phone'];
            $u->placebirth=$data['placebirth'];
            $u->datebirth=$data['datebirth'];
            $u->gender=$data['gender'];
            $u->blood=$data['blood'];
            $u->marital_status=$data['marital_status'];
            $u->religion=$data['religion'];
            $u->image=$data['image'];
            $u->save();

            $u->address()->updateOrCreate(
                ['user_id'=>$u->id],
                [
                    'idtype'=>$data['idtype'] ?? 'ktp',
                    'idnumber'=>$data['idnumber'],
                    'idexpired'=>$data['idexpired'],
                    'ispermanent'=>$data['ispermanent'],
                    'postalcode'=>$data['postalcode'],
                    'citizen_id_address'=>$data['citizen_id_address'],
                    'use_as_residential'=>$data['use_as_residential'],
                    'residential_address'=>$data['residential_address'],
                ]);
            $u->bank()->updateOrCreate([
                'bank_name'=>$data['bank_name'],
                'bank_account'=>$data['bank_account'],
                'bank_account_holder'=>$data['bank_account_holder'],
            ]);
            $u->bpjs()->updateOrCreate(
                ['user_id'=>$u->id],
                [
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

            $updated_emergency_contact = [];
            foreach ($data['emergency_contact'] as $k) {
                $post = $u->emergency_contact()->updateOrCreate(
                    ['name' => $k['name']],
                    $k
                );
                $updated_emergency_contact[] = $post;
            }
            $u->emergency_contact()->whereNotIn('id', collect($updated_emergency_contact)->pluck('id'))->delete();

            $updated_family = [];
            foreach ($data['family'] as $k) {
                $post = $u->family()->updateOrCreate(
                    ['fullname' => $k['fullname']],
                    $k
                );
                $updated_family[] = $post;
            }
            $u->family()->whereNotIn('id', collect($updated_family)->pluck('id'))->delete();

            $updated_formal_education = [];
            foreach ($data['formal_education'] as $k) {
                $post = $u->formal_education()->updateOrCreate(
                    ['institution' => $k['institution']],
                    $k
                );
                $updated_formal_education[] = $post;
            }
            $u->formal_education()->whereNotIn('id', collect($updated_formal_education)->pluck('id'))->delete();

            $updated_informal_education = [];
            foreach ($data['informal_education'] as $k) {
                $post = $u->informal_education()->updateOrCreate(
                    ['name' => $k['name']],
                    $k
                );
                $updated_informal_education[] = $post;
            }
            $u->informal_education()->whereNotIn('id', collect($updated_informal_education)->pluck('id'))->delete();


            $updated_work_experience = [];
            foreach ($data['work_experience'] as $k) {
                $post = $u->work_experience()->updateOrCreate(
                    ['company' => $k['company']],
                    $k
                );
                $updated_work_experience[] = $post;
            }
            $u->work_experience()->whereNotIn('id', collect($updated_work_experience)->pluck('id'))->delete();
            $u->employe()->updateOrCreate(
                ['user_id'=>$u->id],
                [
                    'organization_id'=>$data['organization_id'],
                    'job_position_id'=>$data['job_position_id'],
                    'job_level_id'=>$data['job_level_id'],
                    'approval_line'=>$data['approval_line'],
                    'approval_manager'=>$data['approval_manager'],
                    'branch_id'=>$data['branch_id'],
                    'company_id'=>$data['company_id'],
                    'status'=>$data['status'],
                    'join_date'=>$data['join_date'],
                    'sign_date'=>$data['sign_date'],
                ]);
            $u->salary()->updateOrCreate(
                ['user_id'=>$u->id],
                [
                    'basic_salary'=>$data['basic_salary'],
                    'salary_type'=>$data['salary_type'],
                    'payment_schedule'=>$data['payment_schedule'],
                    'prorate_settings'=>$data['prorate_settings'],
                    'overtime_settings'=>$data['overtime_settings'],
                    'cost_center'=>$data['cost_center'],
                    'cost_center_category'=>$data['cost_center_category'],
                    'currency'=>$data['currency'],
                ]);
            $u->tax_config()->updateOrCreate(
                ['user_id'=>$u->id],
                [
                    'npwp_15_digit_old'=>$data['npwp_15_digit_old'],
                    'npwp_16_digit_new'=>$data['npwp_16_digit_new'],
                    'ptkp_status'=>$data['ptkp_status'],
                    'tax_method'=>$data['tax_method'],
                    'tax_salary'=>$data['tax_salary'],
                    'emp_tax_status'=>$data['emp_tax_status'],
                    'beginning_netto'=>$data['beginning_netto'],
                    'pph21_paid'=>$data['pph21_paid'],
                ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            Notification::make()->title($e->getMessage())->danger()->send();
            dd($e->getMessage());
        }
        return $record;
    }
}
