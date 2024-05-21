<?php

namespace App\Filament\InformationTechnology\Resources\UserManagementResource\Pages;

use App\Filament\InformationTechnology\Resources\UserManagementResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Models\User;

class ViewUserManagement extends ViewRecord
{
    protected static string $resource = UserManagementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $u = User::with('address','bank','bpjs','emergency_contact','family','formal_education','informal_education','work_experience','employe','salary','tax_config')->find($data['id']);
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
}
