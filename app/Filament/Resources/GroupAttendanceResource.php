<?php

namespace App\Filament\Resources;

use App\Classes\MyHelpers;
use App\Filament\Resources\GroupAttendanceResource\Pages;
use App\Filament\Resources\GroupAttendanceResource\RelationManagers;
use App\Models\GroupAttendance;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

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
                Tables\Actions\Action::make('Import Users Group Productions From Excel')
                ->outlined()
                ->icon('fas-file-import')->form([
                    Forms\Components\FileUpload::make('fileImport')
                    ->storeFiles(false)
                    ->columnSpanFull()
                    ->required(),
                ])->action(function (array $data) {
                    $file = $data['fileImport'];
                    $path = $file->getRealPath();
                    $spreadsheet = IOFactory::load($path);
                    
                    // Get the active sheet
                    $sheet = $spreadsheet->getActiveSheet();
                    
                    // Get the header from the second row
                    $header = [];
                    $headerRow = 2;
                    
                    foreach ($sheet->getRowIterator($headerRow, $headerRow) as $row) {
                        $cellIterator = $row->getCellIterator();
                        $cellIterator->setIterateOnlyExistingCells(false);
                        foreach ($cellIterator as $cell) {
                            $headerValue = $cell->getValue();
                            if ($headerValue) {
                                $header[] = $headerValue;
                            }
                        }
                    }
                    
                    // Initialize the array to hold the data
                    $dataArray = [];
                    
                    // Start reading from the third row
                    $startRow = 3;
                    
                    foreach ($sheet->getRowIterator($startRow) as $row) {
                        $rowData = [];
                        $cellIterator = $row->getCellIterator();
                        $cellIterator->setIterateOnlyExistingCells(false);
                        
                        $headerIndex = 0;
                        foreach ($cellIterator as $cell) {
                            if (isset($header[$headerIndex])) {
                                $rowData[$header[$headerIndex]] = $cell->getValue();
                            }
                            $headerIndex++;
                        }
                        
                        $dataArray[] = $rowData;
                    }
                    
                    $helper = new MyHelpers();
                    $groupA = [];
                    $groupB = [];
                    
                    foreach ($dataArray as $key) {
                        $cekUser = $helper->validateUserExistAttendanceSync($key['NIK'], $key['Department'], $key['Jabatan'], $key['NAMA KARYAWAN']);
                        if ($key['GRUP'] == 'JAUHARI/B') {
                            array_push($groupB, $cekUser->id);
                        } else {
                            array_push($groupA, $cekUser->id);
                        }
                    }
                    
                    // Assuming you have a relation defined in GroupAttendance model
                    $groupAInstance = GroupAttendance::where('name', 'GROUP-A')->where('pattern_name', 'production')->first();
                    if ($groupAInstance) {
                        $groupAInstance->user()->sync($groupA);
                    }
                    
                    $groupBInstance = GroupAttendance::where('name', 'GROUP-B')->where('pattern_name', 'production')->first();
                    if ($groupBInstance) {
                        $groupBInstance->user()->sync($groupB);
                    }
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
