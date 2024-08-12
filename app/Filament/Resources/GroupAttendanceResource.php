<?php

namespace App\Filament\Resources;

use App\Classes\MyHelpers;
use App\Filament\Resources\GroupAttendanceResource\Pages;
use App\Filament\Resources\GroupAttendanceResource\RelationManagers;
use App\Models\GroupAttendance;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class GroupAttendanceResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = GroupAttendance::class;

    protected static ?string $navigationLabel = 'Group Attendance';
    protected static ?string $navigationIcon = 'fas-people-group';
    protected static ?string $navigationGroup = 'Attendance Config';

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'name', 'description'
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('user_id')
                    ->label('User members')
                    ->relationship(name: 'user', titleAttribute: 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('pattern_name')
                    ->options([
                        "production"=>"Production",
                        "warehouse"=>"Warehouse",
                        "maintenance"=>"Maintenance",
                        "office"=>"Office",
                        "customs"=>"Customs",
                    ])
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                // START FOR BUTTON IMPORT
                Tables\Actions\Action::make('Import Users Group From Excel')
                ->outlined()
                ->icon('fas-file-import')
                ->form([
                    Forms\Components\Section::make('Import From .xlsx Schedule Biotime')
                        ->description('Make sure you have updated all user data first to help the system validation process when doing this process, and if there are new users make sure you have added / imported them to the user data!')
                        ->schema([
                            Forms\Components\FileUpload::make('fileImport')
                            ->storeFiles(false)
                            ->columnSpanFull()
                            ->required(),
                        ])
                ])->action(function (array $data) {
                    $file = $data['fileImport'];
                    $path = $file->getRealPath();
                    $spreadsheet = IOFactory::load($path);
                    // Get the first sheet
                    $sheet = $spreadsheet->getActiveSheet()->toArray();
                    $originalArray = array_slice($sheet, 1);
                    // Get the current date
                    // $currentDate = Carbon::today();
                    $currentDate = Carbon::now()->day(10);
                    // Initialize an empty array to hold the extracted data
                    $extractedData = [];
                    // Loop through the rows of the sheet
                    foreach ($originalArray as $row) {
                        // Map the row data to the corresponding fields
                        list($nik, $name, $department, $jobPosition) = $row;
                        $extractedData[]=[
                            "nik"=>$nik,
                            "name"=>$name,
                            "department"=>$department,
                            "jobPosition"=>$jobPosition,
                            "date"=>$currentDate->toDateString(),
                            "jam"=>$row[$currentDate->day + 3],
                        ];
                    }
                    $helper = new \App\Classes\MyHelpers();
                    $cek = $helper->addUserGroup($extractedData);
                })
                // END FOR BUTTON DOWNLOAD FORMATED IMPORT
            ])
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pattern_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\ViewAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageGroupAttendances::route('/'),
        ];
    }
}
