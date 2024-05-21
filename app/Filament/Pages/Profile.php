<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Exception;

class Profile extends Page
{
    protected static string $view = 'filament.pages.profile';
    protected static bool $shouldRegisterNavigation = false;
    public ?array $data = [];
    public function mount(): void
    {
        $u = \App\Models\User::with('address','bank','bpjs','emergency_contact','family','formal_education','informal_education','work_experience','employe','salary','tax_config')->find(auth()->user()->id);
        $showdata=[
            "name" => $u->name,
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
        $this->form->fill(
            $showdata
        );
    }

    public function update()
    {
        $data = $this->form->getState();
        DB::beginTransaction();
        try {
            $u = \App\Models\User::find(auth()->user()->id);
            $u->name = $data['name'];
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
            Notification::make()->title('Profile updated successfuly!')->success()->send();
        } catch (Exception $e) {
            DB::rollback();
            Notification::make()->title($e->getMessage())->danger()->send();
        }
    }
    protected function getFormActions(): array
    {
        return [
            Action::make('Update Profile')
                ->color('primary')
                ->submit('Update'),
        ];
    }
    public function form(Form $form): Form
    {
        return $form->schema([
            \Filament\Forms\Components\Split::make([
                \Filament\Forms\Components\Section::make('Personal Information Data')->schema([
                    \Filament\Forms\Components\TextInput::make('name')->maxLength(100)->required(),
                    \Filament\Forms\Components\TextInput::make('email')->email()->maxLength(100)->required(),
                    \Filament\Forms\Components\Select::make('roles')->relationship('roles', 'name')->multiple()->preload()->searchable()->required(),
                    \Filament\Forms\Components\DateTimePicker::make('email_verified_at')->required(),
                    \Filament\Forms\Components\TextInput::make('phone')->numeric()->minLength(11)->maxLength(13)->required(),
                    \Filament\Forms\Components\TextInput::make('placebirth')->maxLength(100)->required(),
                    \Filament\Forms\Components\DatePicker::make('datebirth')->required(),
                    \Filament\Forms\Components\Radio::make('gender')->options(['m'=> 'Man','w'=> 'Woman'])->inlineLabel()->required(),
                    \Filament\Forms\Components\Radio::make('blood')->options(['a'=>'a', 'b'=>'b', 'o'=>'o', 'ab'=>'ab'])->inline()->columnSpanFull()->required(),
                    \Filament\Forms\Components\Radio::make('marital_status')->options(['single'=>'single', 'marriade'=>'marriade', 'widow'=>'widow', 'widower'=>'widower'])->inline()->columnSpanFull()->required(),
                    \Filament\Forms\Components\Radio::make('religion')->options(['islam'=>'islam','protestant'=>'protestant','catholic'=>'catholic','hindu'=>'hindu','buddha'=>'buddha','khonghucu'=>'khonghucu'])->inline()->columnSpanFull()->required(),
                    \Filament\Forms\Components\FileUpload::make('image')->image()->imageEditor()->circleCropper()->directory('user-profile')->columnSpanFull()->required(),
                ])->columns(['sm' => 1,'xl' => 2,'2xl' => 2]),
                \Filament\Forms\Components\Section::make('Personal Address Information Data')->schema([
                    \Filament\Forms\Components\Radio::make('idtype')->options(['ktp'=>'ktp','passport'=>'passport'])->inlineLabel()->columnSpanFull()->required(),
                    \Filament\Forms\Components\TextInput::make('idnumber')->numeric()->minLength(15)->maxLength(17)->columnSpanFull()->required(),
                    \Filament\Forms\Components\Radio::make('ispermanent')->boolean()->inline()->columnSpanFull()->required(),
                    \Filament\Forms\Components\DatePicker::make('idexpired')->required(),
                    \Filament\Forms\Components\TextInput::make('postalcode')->minLength(5)->maxLength(10)->required(),
                    \Filament\Forms\Components\Textarea::make('citizen_id_address')->maxLength(100)->columnSpanFull()->required(),
                    \Filament\Forms\Components\Radio::make('use_as_residential')->boolean()->inline()->columnSpanFull()->required(),
                    \Filament\Forms\Components\Textarea::make('residential_address')->maxLength(100)->columnSpanFull()->required(),
                ])->columns(['sm' => 1, 'xl' => 2, '2xl' => 2]),
            ])->from('md')->columnSpan('full'),
            \Filament\Forms\Components\Split::make([
                \Filament\Forms\Components\Section::make('Personal Bank Used')->schema([
                    \Filament\Forms\Components\TextInput::make('bank_name')->maxLength(10)->required(),
                    \Filament\Forms\Components\TextInput::make('bank_account')->numeric()->maxLength(15)->required(),
                    \Filament\Forms\Components\TextInput::make('bank_account_holder')->maxLength(100)->columnSpanFull()->required(),
                ])->columns(['sm' => 1, 'xl' => 2, '2xl' => 2]),
                \Filament\Forms\Components\Section::make('Password Keys Authentication')->schema([
                    \Filament\Forms\Components\TextInput::make('password')->password()->revealable()->minLength(6)->maxLength(10)->required(),
                    \Filament\Forms\Components\TextInput::make('password_confirmation')->password()->revealable()->minLength(6)->maxLength(10)->required(),
                ])->grow(false),
            ])->from('md')->columnSpan('full'),
            \Filament\Forms\Components\Split::make([
                \Filament\Forms\Components\Section::make('Personal BPJS Used')->schema([
                    \Filament\Forms\Components\TextInput::make('bpjs_ketenagakerjaan')->maxLength(100)->required(),
                    \Filament\Forms\Components\TextInput::make('npp_bpjs_ketenagakerjaan')->label('Label NPP')->maxLength(10)->required(),
                    \Filament\Forms\Components\DatePicker::make('bpjs_ketenagakerjaan_date')->label('BPJS TK Date')->required(),
                    \Filament\Forms\Components\TextInput::make('bpjs_kesehatan')->maxLength(100)->required(),
                    \Filament\Forms\Components\TextInput::make('bpjs_kesehatan_family')->maxLength(100)->required(),
                    \Filament\Forms\Components\DatePicker::make('bpjs_kesehatan_date')->required(),
                    \Filament\Forms\Components\TextInput::make('bpjs_kesehatan_cost')->numeric()->required(),
                    \Filament\Forms\Components\DatePicker::make('jht_cost')->required(),
                    \Filament\Forms\Components\TextInput::make('jaminan_pensiun_cost')->maxLength(100)->required(),
                    \Filament\Forms\Components\DatePicker::make('jaminan_pensiun_date')->required(),
                ])->columns(['sm' => 1, 'xl' => 3, '2xl' => 3]),
                \Filament\Forms\Components\Section::make('Personal Employment Used')->schema([
                    \Filament\Forms\Components\Select::make('organization_id')->options(\App\Models\SystemSetup\Organization::all()->pluck('name', 'id'))->label('Organization')->preload()->required(),
                    \Filament\Forms\Components\Select::make('job_position_id')->options(\App\Models\SystemSetup\JobPosition::all()->pluck('name', 'id'))->label('Job Position')->preload()->required(),
                    \Filament\Forms\Components\Select::make('job_level_id')->options(\App\Models\SystemSetup\JobLevel::all()->pluck('name', 'id'))->label('Level')->preload()->required(),
                    \Filament\Forms\Components\Select::make('approval_line')->options(\App\Models\User::all()->pluck('name', 'id'))->preload()->required(),
                    \Filament\Forms\Components\Select::make('approval_manager')->options(\App\Models\User::all()->pluck('name', 'id'))->preload()->required(),
                    \Filament\Forms\Components\Select::make('branch_id')->options(\App\Models\SystemSetup\Branch::all()->pluck('name', 'id'))->label('Branch')->preload()->required(),
                    \Filament\Forms\Components\Select::make('status')->options(['contract'=>'contract','permanent'=>'permanent','magang'=>'magang','last daily'=>'last daily']),
                    \Filament\Forms\Components\DatePicker::make('join_date'),
                    \Filament\Forms\Components\DatePicker::make('sign_date')
                ])->columns(['sm' => 1, 'xl' => 3, '2xl' => 3])
            ])->from('md')->columnSpan('full'),
            \Filament\Forms\Components\Split::make([
                \Filament\Forms\Components\Section::make('Emergency contacts')->schema([
                    \Filament\Forms\Components\Repeater::make('emergency_contact')->schema([
                        \Filament\Forms\Components\TextInput::make('name')->maxLength(100)->required(),
                        \Filament\Forms\Components\TextInput::make('relationship')->maxLength(100)->required(),
                        \Filament\Forms\Components\TextInput::make('phone')->maxLength(15)->required(),
                        \Filament\Forms\Components\TextInput::make('profesion')->maxLength(100)->required(),
                    ])->columns(['sm' => 1,'xl' => 2,'2xl' => 2])
                ]),
                \Filament\Forms\Components\Section::make('Personal Family Used')->schema([
                    \Filament\Forms\Components\Repeater::make('family')->schema([
                        \Filament\Forms\Components\TextInput::make('fullname')->maxLength(100)->required(),
                        \Filament\Forms\Components\Select::make('relationship')->options(['wife'=>'wife','husband'=>'husband','mother'=>'mother','father'=>'father','brother'=>'brother','sister'=>'sister','child'=>'child'])->required(),
                        \Filament\Forms\Components\DatePicker::make('birthdate')->required(),
                        \Filament\Forms\Components\Select::make('marital_status')->options(['single'=>'single', 'marriade'=>'marriade', 'widow'=>'widow', 'widower'=>'widower'])->required(),
                        \Filament\Forms\Components\TextInput::make('job')->maxLength(100)->columnSpanFull()->required(),
                    ])->columns(['sm' => 1,'xl' => 2,'2xl' => 2])
                ]),
            ])->from('md')->columnSpan('full'),
            \Filament\Forms\Components\Split::make([
                \Filament\Forms\Components\Section::make('Personal Formal Education Used')->schema([
                    \Filament\Forms\Components\Repeater::make('formal_education')->schema([
                        \Filament\Forms\Components\Select::make('grade_id')->options(\App\Models\SystemSetup\GradeEducation::all()->pluck('name', 'id'))->label('Grade')->preload()->required(),
                        \Filament\Forms\Components\TextInput::make('institution')->maxLength(100)->required(),
                        \Filament\Forms\Components\TextInput::make('majors')->maxLength(100)->required(),
                        \Filament\Forms\Components\TextInput::make('score')->maxLength(100)->numeric()->required(),
                        \Filament\Forms\Components\DatePicker::make('start')->required(),
                        \Filament\Forms\Components\DatePicker::make('finish')->required(),
                        \Filament\Forms\Components\TextArea::make('description')->maxLength(100)->required(),
                        \Filament\Forms\Components\Radio::make('certification')->boolean()->required(),
                        \Filament\Forms\Components\FileUpload::make('file')->directory('user-certification-education')->acceptedFileTypes(['application/pdf'])->columnSpanFull()->required(),
                    ])->columns([
                        'sm' => 1,
                        'xl' => 2,
                        '2xl' => 2,
                    ])
                ]),
                \Filament\Forms\Components\Section::make('Personal Informal Education Used')->schema([
                    \Filament\Forms\Components\Repeater::make('informal_education')->schema([
                        \Filament\Forms\Components\TextInput::make('name')->maxLength(100),
                        \Filament\Forms\Components\DatePicker::make('start'),
                        \Filament\Forms\Components\DatePicker::make('finish'),
                        \Filament\Forms\Components\DatePicker::make('expired'),
                        \Filament\Forms\Components\Select::make('type')->options(['day'=>'day', 'month'=>'month', 'year'=>'year']),
                        \Filament\Forms\Components\TextInput::make('duration')->numeric(),
                        \Filament\Forms\Components\TextInput::make('fee')->numeric(),
                        \Filament\Forms\Components\Radio::make('certification')->boolean(),
                        \Filament\Forms\Components\TextArea::make('description')->columnSpanFull()->maxLength(100),
                    ])->columns(['sm' => 1,'xl' => 2,'2xl' => 2])
                ]),
            ])->from('md')->columnSpan('full'),
            \Filament\Forms\Components\Split::make([
                \Filament\Forms\Components\Section::make('Personal Salary Used')->schema([
                    \Filament\Forms\Components\TextInput::make('basic_salary')->numeric()->required(),
                    \Filament\Forms\Components\Select::make('salary_type')->options(['Monthly'=>'Monthly', 'Weakly'=>'Weakly', 'Dayly'=>'Dayly'])->required(),
                    \Filament\Forms\Components\TextInput::make('payment_schedule')->required(),
                    \Filament\Forms\Components\TextInput::make('prorate_settings')->required(),
                    \Filament\Forms\Components\TextInput::make('overtime_settings')->required(),
                    \Filament\Forms\Components\TextInput::make('cost_center')->required(),
                    \Filament\Forms\Components\TextInput::make('cost_center_category')->required(),
                    \Filament\Forms\Components\TextInput::make('currency')->required(),
                ])->columns(['sm' => 1,'xl' => 2,'2xl' => 2]),
                \Filament\Forms\Components\Section::make('Personal Tax Config Used')->schema([
                    \Filament\Forms\Components\TextInput::make('npwp_15_digit_old')->numeric()->required(),
                    \Filament\Forms\Components\TextInput::make('npwp_16_digit_new')->numeric()->required(),
                    \Filament\Forms\Components\Select::make('ptkp_status')->options(['TK0'=>'TK0','TK1'=>'TK1','TK2'=>'TK2','TK3'=>'TK3','K0'=>'K0','K1'=>'K1','K2'=>'K2','K3'=>'K3','K/I/0'=>'K/I/0','K/I/1'=>'K/I/1','K/I/2'=>'K/I/2','K/I/3'=>'K/I/3'])->required(),
                    \Filament\Forms\Components\Select::make('tax_method')->options(['gross'])->required(),
                    \Filament\Forms\Components\Select::make('tax_salary')->options(['taxable'])->required(),
                    \Filament\Forms\Components\Select::make('emp_tax_status')->options(['permanent'=>'permanent', 'contract'=>'contract', 'last-daily'=>'last-daily'])->required(),
                    \Filament\Forms\Components\TextInput::make('beginning_netto')->numeric()->required(),
                    \Filament\Forms\Components\TextInput::make('pph21_paid')->numeric()->required()
                ])->columns(['sm' => 1,'xl' => 2,'2xl' => 2]),
            ])->from('md')->columnSpan('full'),
            \Filament\Forms\Components\Section::make('Work Experience Used')->schema([
                \Filament\Forms\Components\Repeater::make('work_experience')->schema([
                    \Filament\Forms\Components\TextInput::make('company')->required(),
                    \Filament\Forms\Components\TextInput::make('position')->required(),
                    \Filament\Forms\Components\DatePicker::make('from')->required(),
                    \Filament\Forms\Components\DatePicker::make('to')->required(),
                    \Filament\Forms\Components\TextInput::make('length_of_service')->columnSpanFull()->required(),
                ])->columns(['sm' => 1,'xl' => 4,'2xl' => 4])
            ])
        ])->statePath('data')->model(auth()->user());
    }
}
