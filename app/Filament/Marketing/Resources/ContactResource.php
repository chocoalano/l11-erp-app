<?php

namespace App\Filament\Marketing\Resources;

use App\Filament\Marketing\Resources\ContactResource\Pages;
use App\Filament\Marketing\Resources\ContactResource\RelationManagers;
use App\Models\Marketing\Digital\Contact;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ContactResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Contact::class;

    protected static ?string $navigationLabel = 'Contact';
    protected static ?string $navigationIcon = 'fas-address-book';
    protected static ?string $navigationGroup = 'Company Profile';
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
            'hp'
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                ->required(),
                Forms\Components\TextInput::make('hp')
                ->minLength(11)
                ->maxLength(15)
                ->required(),
                Forms\Components\Toggle::make('active')
                ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->limit(35),
                Tables\Columns\TextColumn::make('hp')->limit(35),
                Tables\Columns\TextColumn::make('active')
                ->badge()
                ->label('Status')
                ->formatStateUsing(function ($state) {
                    return $state ? 'Active' : 'Inactive';
                })
                ->color(fn (string $state): string => match ($state) {
                    '0' => 'success',
                    '1' => 'danger',
                }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ManageContacts::route('/'),
        ];
    }
}
