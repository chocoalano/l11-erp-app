<?php

namespace App\Filament\InformationTechnology\Resources;

use App\Filament\InformationTechnology\Resources\UserResource\Pages;
use App\Models\Organization;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-s-user-group';
    protected static ?string $navigationLabel = 'User Management';
    protected static ?string $navigationGroup = 'Authorization';
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
            'name',
            'nik',
            'email',
            'phone',
            'placebirth',
            'datebirth',
            'gender',
            'blood',
            'marital_status',
            'religion'
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->disabled()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nik')
                    ->disabled()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->disabled()
                    ->maxLength(255),
                Forms\Components\Select::make('roles')
                    ->relationship(name: 'rolefind', titleAttribute: 'name')
                    ->label('Role Access')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->required(),
                Forms\Components\DateTimePicker::make('email_verified_at')
                    ->disabled(),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->disabled()
                    ->maxLength(20),
                Forms\Components\TextInput::make('placebirth')
                    ->maxLength(20)
                    ->disabled(),
                Forms\Components\DatePicker::make('datebirth')
                ->disabled(),
                Forms\Components\TextInput::make('gender')
                ->disabled(),
                Forms\Components\TextInput::make('blood')
                ->disabled(),
                Forms\Components\TextInput::make('marital_status')
                ->disabled(),
                Forms\Components\TextInput::make('religion')
                ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $org = Organization::where('name', 'ICT')->first();
                return $query->whereHas('employe', function($query) use ($org){
                    $query->where('organization_id', $org->id);
                }); 
            })
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nik')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('placebirth')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('datebirth')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('gender')
                    ->badge()
                    ->label('Gender')
                    ->formatStateUsing(function ($state) {
                        return $state === 'm' ? 'Man' : 'Woman';
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'm' => 'success',
                        'w' => 'danger',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('blood')
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        switch ($state) {
                            case 'a':
                                return 'Gol A';
                            case 'b':
                                return 'Gol B';
                            case 'o':
                                return 'Gol O';
                            case 'ab':
                                return 'Gol AB';
                            
                            default:
                                return 'Gol A';
                        }
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'a' => 'success',
                        'b' => 'danger',
                        'o' => 'danger',
                        'ab' => 'danger',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('marital_status')
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        switch ($state) {
                            case 'single':
                                return 'Single';
                            case 'marriade':
                                return 'Marry';
                            case 'widow':
                                return 'Widow';
                            case 'widower':
                                return 'Widower';
                            
                            default:
                                return 'Gol A';
                        }
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'single' => 'success',
                        'marriade' => 'danger',
                        'widow' => 'warning',
                        'widower' => 'primary',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('religion')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'islam' => 'success',
                        'protestant' => 'danger',
                        'catholic' => 'warning',
                        'hindu' => 'info',
                        'buddha' => 'primary',
                        'khonghucu' => 'gray',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                DateRangeFilter::make('created_at')
            ])
            ->actions([
                Tables\Actions\EditAction::make()
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
            'index' => Pages\ManageUsers::route('/'),
        ];
    }
}
