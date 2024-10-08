<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserManagementResource\Pages;
use App\Filament\Resources\UserManagementResource\RelationManagers;
use App\Models\Organization;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use App\Jobs\ProcessImportUserBiotime;

class UserManagementResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'fas-user-plus';

    public static function getGloballySearchableAttributes(): array
    {
        return [
        'roles.name',
        'name',
        'email',
        'password',
        'phone',
        'placebirth',
        'datebirth',
        'gender',
        'blood',
        'marital_status',
        'religion',
    ];
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'restore',
            'restore_any',
            'replicate',
            'reorder',
            'delete',
            'delete_any',
            'force_delete',
            'force_delete_any',
            'export_excel',
            'import_excel',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Split::make([
                Forms\Components\Section::make('Personal Information Data')->schema([
                    Forms\Components\TextInput::make('name')->maxLength(100)->required(),
                    Forms\Components\TextInput::make('nik')->unique(table: User::class)->maxLength(10)->numeric()->required(),
                    Forms\Components\TextInput::make('email')->unique(table: User::class)->email()->maxLength(100)->required(),
                    Forms\Components\Select::make('roles')
                    ->options(\Spatie\Permission\Models\Role::all()->pluck('name', 'id'))
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->required(),
                    Forms\Components\DateTimePicker::make('email_verified_at')->required(),
                    Forms\Components\TextInput::make('phone')->numeric()->minLength(11)->maxLength(13)->required(),
                    Forms\Components\TextInput::make('placebirth')->maxLength(100)->required(),
                    Forms\Components\DatePicker::make('datebirth')->required(),
                    Forms\Components\Radio::make('gender')->options(['m'=> 'Man','w'=> 'Woman'])->inlineLabel()->required(),
                    Forms\Components\Radio::make('blood')->options(['a'=>'a', 'b'=>'b', 'o'=>'o', 'ab'=>'ab'])->inline()->columnSpanFull()->required(),
                    Forms\Components\Radio::make('marital_status')->options(['single'=>'single', 'marriade'=>'marriade', 'widow'=>'widow', 'widower'=>'widower'])->inline()->columnSpanFull()->required(),
                    Forms\Components\Radio::make('religion')->options(['islam'=>'islam','protestant'=>'protestant','catholic'=>'catholic','hindu'=>'hindu','buddha'=>'buddha','khonghucu'=>'khonghucu'])->inline()->columnSpanFull()->required(),
                    Forms\Components\FileUpload::make('image')->image()->imageEditor()->circleCropper()->directory('user-profile')->columnSpanFull()->required(),
                ])->columns(['sm' => 1,'xl' => 2,'2xl' => 2]),
                Forms\Components\Section::make('Personal Address Information Data')->schema([
                    Forms\Components\Radio::make('idtype')->options(['ktp'=>'ktp','passport'=>'passport'])->inlineLabel()->columnSpanFull(),
                    Forms\Components\TextInput::make('idnumber')->numeric()->minLength(15)->maxLength(17)->columnSpanFull(),
                    Forms\Components\Radio::make('ispermanent')->boolean()->inline()->columnSpanFull(),
                    Forms\Components\DatePicker::make('idexpired'),
                    Forms\Components\TextInput::make('postalcode')->minLength(5)->maxLength(10),
                    Forms\Components\Textarea::make('citizen_id_address')->maxLength(100)->columnSpanFull(),
                    Forms\Components\Radio::make('use_as_residential')->boolean()->inline()->columnSpanFull(),
                    Forms\Components\Textarea::make('residential_address')->maxLength(100)->columnSpanFull(),
                ])->columns(['sm' => 1, 'xl' => 2, '2xl' => 2]),
            ])->from('md')->columnSpan('full'),
            Forms\Components\Split::make([
                Forms\Components\Section::make('Personal Bank Used')->schema([
                    Forms\Components\TextInput::make('bank_name')->maxLength(10),
                    Forms\Components\TextInput::make('bank_account')->numeric()->maxLength(15),
                    Forms\Components\TextInput::make('bank_account_holder')->maxLength(100)->columnSpanFull(),
                ])->columns(['sm' => 1, 'xl' => 2, '2xl' => 2]),
                Forms\Components\Section::make('Password Keys Authentication')->schema([
                    Forms\Components\TextInput::make('password')->password()->revealable()->minLength(6)->maxLength(10),
                    Forms\Components\TextInput::make('password_confirmation')->password()->revealable()->minLength(6)->maxLength(10),
                ])->grow(false),
            ])->from('md')->columnSpan('full'),
            Forms\Components\Split::make([
                Forms\Components\Section::make('Personal BPJS Used')->schema([
                    Forms\Components\TextInput::make('bpjs_ketenagakerjaan')->maxLength(100),
                    Forms\Components\TextInput::make('npp_bpjs_ketenagakerjaan')->label('Label NPP')->maxLength(10),
                    Forms\Components\DatePicker::make('bpjs_ketenagakerjaan_date')->label('BPJS TK Date'),
                    Forms\Components\TextInput::make('bpjs_kesehatan')->maxLength(100),
                    Forms\Components\TextInput::make('bpjs_kesehatan_family')->maxLength(100),
                    Forms\Components\DatePicker::make('bpjs_kesehatan_date'),
                    Forms\Components\TextInput::make('bpjs_kesehatan_cost')->numeric(),
                    Forms\Components\DatePicker::make('jht_cost'),
                    Forms\Components\TextInput::make('jaminan_pensiun_cost')->maxLength(100),
                    Forms\Components\DatePicker::make('jaminan_pensiun_date'),
                ])->columns(['sm' => 1, 'xl' => 3, '2xl' => 3]),
                Forms\Components\Section::make('Personal Employment Used')->schema([
                    Forms\Components\Select::make('organization_id')->options(\App\Models\Organization::all()->pluck('name', 'id'))->label('Organization')->preload()->required()->createOptionForm([
                        Forms\Components\Section::make()->schema([
                            Forms\Components\TextInput::make('name')->maxLength(100)->required(),
                            Forms\Components\TextInput::make('description')->maxLength(100)->required(),
                        ])->columns(['sm' => 1,'xl' => 2,'2xl' => 2])
                    ])->createOptionUsing(function (array $data): int {
                        $create = new \App\Models\Organization();
                        $create->name=$data['name'];
                        $create->description=$data['description'];
                        $create->save();
                        return $create->id;
                    }),
                    Forms\Components\Select::make('job_position_id')->options(\App\Models\JobPosition::all()->pluck('name', 'id'))->label('Job Position')->preload()->required()->createOptionForm([
                        Forms\Components\Section::make()->schema([
                            Forms\Components\TextInput::make('name')->maxLength(100)->required(),
                            Forms\Components\Textarea::make('description')->required(),
                        ])->columns(['sm' => 1,'xl' => 1,'2xl' => 1])
                    ])->createOptionUsing(function (array $data): int {
                        $create = new \App\Models\JobPosition();
                        $create->name=$data['name'];
                        $create->description=$data['description'];
                        $create->save();
                        return $create->id;
                    }),
                    Forms\Components\Select::make('job_level_id')->options(\App\Models\JobLevel::all()->pluck('name', 'id'))->label('Level')->preload()->required()->createOptionForm([
                        Forms\Components\Section::make()->schema([
                            Forms\Components\TextInput::make('name')->maxLength(100)->required(),
                            Forms\Components\Textarea::make('description')->required(),
                        ])->columns(['sm' => 1,'xl' => 1,'2xl' => 1])
                    ])->createOptionUsing(function (array $data): int {
                        $create = new \App\Models\JobLevel();
                        $create->name=$data['name'];
                        $create->description=$data['description'];
                        $create->save();
                        return $create->id;
                    }),
                    Forms\Components\Select::make('approval_line')->options(User::all()->pluck('name', 'id'))->preload()->required(),
                    Forms\Components\Select::make('approval_manager')->options(User::all()->pluck('name', 'id'))->preload()->required(),
                    Forms\Components\Select::make('company_id')->options(\App\Models\Company::all()->pluck('name', 'id'))->label('Company')->preload()->required()->createOptionForm([
                        Forms\Components\Section::make([
                            Forms\Components\TextInput::make('name')->minLength(1)->maxLength(100)->required(),
                            Forms\Components\TextInput::make('latitude')->numeric()->required(),
                            Forms\Components\TextInput::make('longitude')->numeric()->required(),
                            Forms\Components\Textarea::make('full_address')->columnSpanFull()->required(),
                        ])->columns(['sm' => 1,'xl' => 3,'2xl' => 3,]),
                    ])->createOptionUsing(function (array $data): int {
                        $create = new \App\Models\Company();
                        $create->name=$data['name'];
                        $create->latitude=$data['latitude'];
                        $create->longitude=$data['longitude'];
                        $create->full_address=$data['full_address'];
                        $create->save();
                        return $create->id;
                    }),
                    Forms\Components\Select::make('branch_id')->options(\App\Models\Branch::all()->pluck('name', 'id'))->label('Branch')->preload()->required()->createOptionForm([
                        Forms\Components\Section::make([
                            Forms\Components\TextInput::make('name')->minLength(1)->maxLength(100)->required(),
                            Forms\Components\TextInput::make('latitude')->numeric()->required(),
                            Forms\Components\TextInput::make('longitude')->numeric()->required(),
                            Forms\Components\Textarea::make('full_address')->columnSpanFull()->required(),
                        ])->columns(['sm' => 1,'xl' => 3,'2xl' => 3,]),
                    ])->createOptionUsing(function (array $data): int {
                        $create = new \App\Models\Branch();
                        $create->name=$data['name'];
                        $create->latitude=$data['latitude'];
                        $create->longitude=$data['longitude'];
                        $create->full_address=$data['full_address'];
                        $create->save();
                        return $create->id;
                    }),
                    Forms\Components\Select::make('status')->options(['contract'=>'contract','permanent'=>'permanent','magang'=>'magang','last daily'=>'last daily']),
                    Forms\Components\DatePicker::make('join_date'),
                    Forms\Components\DatePicker::make('sign_date')
                ])->columns(['sm' => 1, 'xl' => 3, '2xl' => 3])
            ])->from('md')->columnSpan('full'),
            Forms\Components\Split::make([
                Forms\Components\Section::make('Emergency contacts')->schema([
                    Forms\Components\Repeater::make('emergency_contact')->schema([
                        Forms\Components\TextInput::make('name')->maxLength(100),
                        Forms\Components\TextInput::make('relationship')->maxLength(100),
                        Forms\Components\TextInput::make('phone')->maxLength(15),
                        Forms\Components\TextInput::make('profesion')->maxLength(100),
                    ])->columns(['sm' => 1,'xl' => 2,'2xl' => 2])
                ]),
                Forms\Components\Section::make('Personal Family Used')->schema([
                    Forms\Components\Repeater::make('family')->schema([
                        Forms\Components\TextInput::make('fullname')->maxLength(100),
                        Forms\Components\Select::make('relationship')->options(['wife'=>'wife','husband'=>'husband','mother'=>'mother','father'=>'father','brother'=>'brother','sister'=>'sister','child'=>'child']),
                        Forms\Components\DatePicker::make('birthdate'),
                        Forms\Components\Select::make('marital_status')->options(['single'=>'single', 'marriade'=>'marriade', 'widow'=>'widow', 'widower'=>'widower']),
                        Forms\Components\TextInput::make('job')->maxLength(100)->columnSpanFull(),
                    ])->columns(['sm' => 1,'xl' => 2,'2xl' => 2])
                ]),
            ])->from('md')->columnSpan('full'),
            Forms\Components\Split::make([
                Forms\Components\Section::make('Personal Formal Education Used')->schema([
                    Forms\Components\Repeater::make('formal_education')->schema([
                        Forms\Components\Select::make('grade_id')->options(\App\Models\GradeEducation::all()->pluck('name', 'id'))->label('Grade')->preload()->createOptionForm([
                            Forms\Components\Section::make()->schema([
                                Forms\Components\TextInput::make('name')->maxLength(100)
                            ])->columns(['sm' => 1,'xl' => 1,'2xl' => 1])
                        ])->createOptionUsing(function (array $data): int {
                            $create = new \App\Models\GradeEducation();
                            $create->name=$data['name'];
                            $create->save();
                            return $create->id;
                        }),
                        Forms\Components\TextInput::make('institution')->maxLength(100),
                        Forms\Components\TextInput::make('majors')->maxLength(100),
                        Forms\Components\TextInput::make('score')->maxLength(100)->numeric(),
                        Forms\Components\DatePicker::make('start'),
                        Forms\Components\DatePicker::make('finish'),
                        Forms\Components\TextArea::make('description')->maxLength(100),
                        Forms\Components\Radio::make('certification')->boolean(),
                        Forms\Components\FileUpload::make('file')->directory('user-certification-education')->acceptedFileTypes(['application/pdf'])->columnSpanFull(),
                    ])->columns([
                        'sm' => 1,
                        'xl' => 2,
                        '2xl' => 2,
                    ])
                ]),
                Forms\Components\Section::make('Personal Informal Education Used')->schema([
                    Forms\Components\Repeater::make('informal_education')->schema([
                        Forms\Components\TextInput::make('name')->maxLength(100),
                        Forms\Components\DatePicker::make('start'),
                        Forms\Components\DatePicker::make('finish'),
                        Forms\Components\DatePicker::make('expired'),
                        Forms\Components\Select::make('type')->options(['day'=>'day', 'month'=>'month', 'year'=>'year']),
                        Forms\Components\TextInput::make('duration')->numeric(),
                        Forms\Components\TextInput::make('fee')->numeric(),
                        Forms\Components\Radio::make('certification')->boolean(),
                        Forms\Components\TextArea::make('description')->columnSpanFull()->maxLength(100),
                    ])->columns(['sm' => 1,'xl' => 2,'2xl' => 2])
                ]),
            ])->from('md')->columnSpan('full'),
            Forms\Components\Split::make([
                Forms\Components\Section::make('Personal Salary Used')->schema([
                    Forms\Components\TextInput::make('basic_salary')->numeric(),
                    Forms\Components\Select::make('salary_type')->options(['Monthly'=>'Monthly', 'Weakly'=>'Weakly', 'Dayly'=>'Dayly']),
                    Forms\Components\TextInput::make('payment_schedule'),
                    Forms\Components\TextInput::make('prorate_settings'),
                    Forms\Components\TextInput::make('overtime_settings'),
                    Forms\Components\TextInput::make('cost_center'),
                    Forms\Components\TextInput::make('cost_center_category'),
                    Forms\Components\TextInput::make('currency'),
                ])->columns(['sm' => 1,'xl' => 2,'2xl' => 2]),
                Forms\Components\Section::make('Personal Tax Config Used')->schema([
                    Forms\Components\TextInput::make('npwp_15_digit_old')->numeric(),
                    Forms\Components\TextInput::make('npwp_16_digit_new')->numeric(),
                    Forms\Components\Select::make('ptkp_status')->options(['TK0'=>'TK0','TK1'=>'TK1','TK2'=>'TK2','TK3'=>'TK3','K0'=>'K0','K1'=>'K1','K2'=>'K2','K3'=>'K3','K/I/0'=>'K/I/0','K/I/1'=>'K/I/1','K/I/2'=>'K/I/2','K/I/3'=>'K/I/3']),
                    Forms\Components\Select::make('tax_method')->options(['gross' => 'Gross']),
                    Forms\Components\Select::make('tax_salary')->options(['taxable' => 'Taxable']),
                    Forms\Components\Select::make('emp_tax_status')->options(['permanent'=>'permanent', 'contract'=>'contract', 'last-daily'=>'last-daily']),
                    Forms\Components\TextInput::make('beginning_netto')->numeric(),
                    Forms\Components\TextInput::make('pph21_paid')->numeric()
                ])->columns(['sm' => 1,'xl' => 2,'2xl' => 2]),
            ])->from('md')->columnSpan('full'),
            Forms\Components\Section::make('Work Experience Used')->schema([
                Forms\Components\Repeater::make('work_experience')->schema([
                    Forms\Components\TextInput::make('company'),
                    Forms\Components\TextInput::make('position'),
                    Forms\Components\DatePicker::make('from'),
                    Forms\Components\DatePicker::make('to'),
                    Forms\Components\TextInput::make('length_of_service')->columnSpanFull(),
                ])->columns(['sm' => 1,'xl' => 4,'2xl' => 4])
            ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->modifyQueryUsing(function (Builder $query) {
            $u = User::find(auth()->user()->id);
            if ($u->hasRole(['super_admin', 'Administrator HR'])) {
                return $query;
            }else{
                $usr = User::with('employe')->where('id', auth()->user()->id)->first();
                $org = Organization::with('employe')->where('id', $usr->employe->organization_id)->first();
                $userId = [];
                foreach ($org->employe as $k) {
                    array_push($userId, $k->user_id);
                }
                return $query->whereIn('id', $userId); 
            } 
        })
        ->headerActions([
            Tables\Actions\ActionGroup::make([
                // START FOR BUTTON DOWNLOAD FORMATED IMPORT
                Tables\Actions\Action::make('Download Excel For Import Data')
                    ->icon('fas-file-excel')
                    ->outlined()
                    ->url(route('download.user.format.excel'))
                    ->openUrlInNewTab(),
                // END FOR BUTTON DOWNLOAD FORMATED IMPORT
                // START FOR BUTTON IMPORT
                Tables\Actions\Action::make('Import From Excel')
                    ->icon('fas-file-import')
                    ->outlined()
                    ->form([
                        Forms\Components\FileUpload::make('fileImport')
                        ->storeFiles(false)
                        ->columnSpanFull()
                        ->required(),
                    ])
                    ->action(function (array $data): void {
                        $file = $data['fileImport'];
                        $path = $file->getRealPath();
                        $ss = IOFactory::load($path);
                        $sheet = $ss->getActiveSheet();
                        $highestColumnIndex = $sheet->getHighestDataColumn();
                        $headers = $sheet->rangeToArray('A1:' . $highestColumnIndex . '1', null, true, false)[0];
                        $datarow = [];
                        foreach ($sheet->getRowIterator(2) as $row) {
                            $rowData = [];
                            $cellIterator = $row->getCellIterator();
                            $cellIterator->setIterateOnlyExistingCells(false);
                            foreach ($cellIterator as $cell) {
                                $columnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($cell->getColumn()) - 1;
                                $value = $cell->getValue();
                                $header = $headers[$columnIndex];
                                $rowData[$header] = $value;
                            }
                            $datarow[] = $rowData;
                        }
                        $u = new \App\Http\Controllers\Web\UserController();
                        $exec = $u->importFormatExcelImport($datarow);
                        if($exec === 'success import!'){
                            Notification::make()
                            ->title('Import successfully')
                            ->success()
                            ->send();
                        }else{
                            Notification::make()
                            ->title('Import Unsuccessfully')
                            ->success()
                            ->body($exec)
                            ->send();
                        }
                    }),
                Tables\Actions\Action::make('Import From Excel Biotime')
                    ->icon('fas-file-import')
                    ->outlined()
                    ->color('info')
                    ->form([
                        Forms\Components\FileUpload::make('fileImport')
                        ->storeFiles(false)
                        ->columnSpanFull()
                        ->required(),
                    ])
                    ->action(function (array $data): void {
                        $file = $data['fileImport'];
                        $path = $file->getRealPath();
                        $spreadsheet = IOFactory::load($path);
                        $worksheet = $spreadsheet->getActiveSheet();
                        $employeeData = [];

                        // Loop melalui baris data (mulai dari baris kedua jika baris pertama adalah header)
                        foreach ($worksheet->getRowIterator(2) as $row) {
                            $cellIterator = $row->getCellIterator();
                            $cellIterator->setIterateOnlyExistingCells(false); // Loop semua sel dalam baris, termasuk sel yang kosong
                            $rowData = [];
                            foreach ($cellIterator as $cell) {
                                $rowData[] = $cell->getValue();
                            }

                            // Validasi dan konversi tanggal
                            $joinDate = (isset($rowData[9]) && is_numeric($rowData[9])) ? 
                                Date::excelToDateTimeObject($rowData[9])->format('Y-m-d') : null;
                            $birthDate = (isset($rowData[15]) && is_numeric($rowData[15])) ? 
                                Date::excelToDateTimeObject($rowData[15])->format('Y-m-d') : null;
                            // Periksa apakah baris memiliki data yang terisi
                            $isRowEmpty = true;
                            foreach ($rowData as $data) {
                                if (!empty($data)) {
                                    $isRowEmpty = false;
                                    break;
                                }
                            }
                            // Masukkan data ke dalam array dengan key yang sesuai hanya jika baris tidak kosong
                            if (!$isRowEmpty) {
                                $employeeData[] = [
                                    'nik' => $rowData[0] ?? null,
                                    'nama' => $rowData[1] ?? null,
                                    'dept' => $rowData[2] ?? null,
                                    'position' => $rowData[3] ?? null,
                                    'level' => $rowData[4] ?? null,
                                    'atasan' => $rowData[5] ?? null,
                                    'grade' => $rowData[6] ?? null,
                                    'emp_status' => $rowData[7] ?? null,
                                    'area_kerja' => $rowData[8] ?? null,
                                    'tgl_bergabung' => $joinDate,
                                    'no_ktp' => $rowData[10] ?? null,
                                    'no_npwp' => $rowData[11] ?? null,
                                    'no_hp' => $rowData[12] ?? null,
                                    'email' => $rowData[13] ?? null,
                                    'placebirth' => $rowData[14] ?? null,
                                    'datebirth' => $birthDate,
                                    'religion' => $rowData[16] ?? null,
                                    'gender' => $rowData[17] ?? null,
                                    'status_pernikahan' => $rowData[18] ?? null,
                                ];
                            }
                        }
                        $helper = new \App\Classes\MyHelpers();
                        foreach ($employeeData as $k) {
                            $helper->validateUserExist($k);
                        }
                    }),
                // END FOR BUTTON IMPORT
                // START FOR BUTTON EXPORT DATA TO .XLSX
                Tables\Actions\Action::make('Export To Excel')
                    ->outlined()
                    ->icon('fas-file-export')
                    ->url(route('download.user.format.excel'))
                    ->openUrlInNewTab(),
                // END FOR BUTTON EXPORT DATA TO .XLSX
            ])
            ->label('More actions')
            ->icon('heroicon-m-ellipsis-vertical')
            ->color('primary')
            ->button()
        ])
        ->columns([
            Tables\Columns\TextColumn::make('No.')->rowIndex(),
            Tables\Columns\ImageColumn::make('image')->circular(),
            Tables\Columns\TextColumn::make('nik')->searchable(),
            Tables\Columns\TextColumn::make('name')->searchable(),
            Tables\Columns\TextColumn::make('email')->searchable(),
            Tables\Columns\TextColumn::make('phone')->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('placebirth')->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('datebirth')->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('gender')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'm' => 'success',
                    'w' => 'danger',
                })
                ->formatStateUsing(fn (string $state): string => strtoupper($state === 'm' ? 'pria' : 'wanita'))
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('employe.organization_id')
                ->formatStateUsing(fn (string $state, \App\Models\Organization $organization): string => $organization::findOrFail($state)->name)
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('blood')->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('marital_status')->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('religion')->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('email_verified_at')->dateTime()->sortable()
            ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()
            ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()
            ->toggleable(isToggledHiddenByDefault: true),
        ])
        ->filters([
            Tables\Filters\TrashedFilter::make(),
            Tables\Filters\SelectFilter::make('roles')
            ->relationship('roles', 'name')
            ->multiple()
            ->preload()
            ->searchable()
            ->options(
                fn (): array => \Spatie\Permission\Models\Role::query()
                ->pluck('name', 'id')
                ->all()
            )
        ])
        ->actions([
            Tables\Actions\ActionGroup::make([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\ReplicateAction::make()
                    ->beforeReplicaSaved(function (User $replica): void {
                        $carbonDate = \Illuminate\Support\Carbon::now();
                        $datetime = $carbonDate->format('YmdHis');
                        $replica->name = "[new]$datetime._$replica->name";
                        $replica->nik = $datetime;
                        $replica->email = "[new]$datetime._$replica->email";
                    })->requiresConfirmation()
                    ->modalHeading('Replicate Data')
                    ->modalDescription('Are you sure you\'d like to replicate this data? This cannot be undone.')
                    ->modalSubmitActionLabel('Yes, replicate it'),
                Tables\Actions\RestoreAction::make(),
            ])
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]),
        ]);
    }

    public static function getRelations(): array
    {
        return [
                //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserManagement::route('/'),
            'create' => Pages\CreateUserManagement::route('/create'),
            'view' => Pages\ViewUserManagement::route('/{record}'),
            'edit' => Pages\EditUserManagement::route('/{record}/edit'),
        ];
    }
    public static function getNavigationGroup(): ?string
    {
        return \BezhanSalleh\FilamentShield\Support\Utils::isResourceNavigationGroupEnabled()
        ? __('Authorization')
        : '';
    }
}
