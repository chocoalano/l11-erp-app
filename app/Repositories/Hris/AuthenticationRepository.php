<?php

namespace App\Repositories\Hris;

use App\Interfaces\Hris\AuthenticationInterface;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;

class AuthenticationRepository implements AuthenticationInterface
{
    protected $model;
    /**
     * Create a new class instance.
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }
    
    public function login($emailOrNik, $password){
        $loginField = filter_var($emailOrNik, FILTER_VALIDATE_EMAIL) ? 'email' : 'nik';
        $auth = Auth::attempt([$loginField => $emailOrNik, 'password' => $password]);
        return $auth ? Auth::user() : null ;
    }
    public function profile(){
        $auth = Auth::user();
        $user = User::with(
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
        'tax_config',
        'group_attendance',
        )->find($auth->id);
        $roles = $user->roles;
        $user->getAllPermissions();
        return ['user'=>$user, 'authorization'=>$roles];
    }
    public function updateProfile($data){
        try {
            $u = User::find(Auth::user()->id);
            $u->name = $data['name'];
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

            DB::table('model_has_roles')->where('model_id',$u->id)->delete();
            foreach($data['roles'] as $k){
                $r = DB::table('roles')->where('id', $k)->first();
                $u->assignRole($r->name);
            }

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
                return $u;
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
    public function logout($tokenId){
        $token = PersonalAccessToken::find($tokenId);

        if ($token) {
            $token->delete();
            return true;
        }

        return false;
    }
}
