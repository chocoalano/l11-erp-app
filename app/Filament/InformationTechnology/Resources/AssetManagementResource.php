<?php

namespace App\Filament\InformationTechnology\Resources;

use App\Filament\InformationTechnology\Resources\AssetManagementResource\Pages;
use App\Filament\InformationTechnology\Resources\AssetManagementResource\RelationManagers;
use App\Models\IT\AssetManagement;
use App\Models\User;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class AssetManagementResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = AssetManagement::class;

    protected static ?string $navigationLabel = 'Units';
    protected static ?string $navigationIcon = 'fas-code-branch';
    protected static ?string $navigationGroup = 'Asset Management';

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'replicate',
            'delete',
            'delete_any',
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'company.name',
            'asset_tag',
            'model.name',
            'status.name',
            'room.name',
            'pic.name',
            'notes',
            'purchase_at',
            'purchase_price',
            'suppliers',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('company_id')
                    ->relationship('company', 'name')
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('asset_tag')
                    ->prefix('Generated')
                    ->suffixAction(
                        Action::make('generateAssetNumbersCode')
                            ->icon('fas-arrows-rotate')
                            ->requiresConfirmation()
                            ->action(function (Set $set, $state) {
                                $code = new AssetManagement();
                                $state = $code->generateUniqueCode();
                                $set('asset_tag', $state);
                            })
                    )
                    ->required()
                    ->maxLength(100),
                Forms\Components\Select::make('model_id')
                    ->relationship('model', 'name')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->required(),
                        Forms\Components\Select::make('types_of_goods')
                            ->options([
                                'physique' => 'Physique',
                                'unphysique' => 'Unphysique',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('category')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->required(),
                Forms\Components\Select::make('status_id')
                    ->relationship('status', 'name')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->required(),
                Forms\Components\Select::make('room_id')
                    ->relationship('room', 'name')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->required(),
                Forms\Components\Select::make('pic')
                    ->searchable()
                    ->relationship('user', 'name')
                    ->preload()
                    ->required(),
                Forms\Components\RichEditor::make('notes')
                    ->toolbarButtons([
                        'blockquote',
                        'bold',
                        'bulletList',
                        'codeBlock',
                        'h2',
                        'h3',
                        'italic',
                        'link',
                        'orderedList',
                        'redo',
                        'strike',
                        'underline',
                        'undo',
                    ])
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->imageEditor()
                    ->imageEditorViewportWidth('1920')
                    ->imageEditorViewportHeight('1080')
                    ->directory('asset-management'),
                Forms\Components\DatePicker::make('purchase_at'),
                Forms\Components\TextInput::make('purchase_price')
                    ->numeric(),
                Forms\Components\TextInput::make('suppliers')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('asset_tag')
                    ->searchable(),
                Tables\Columns\TextColumn::make('model.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('room.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable(),
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('purchase_at')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('purchase_price')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('suppliers')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                DateRangeFilter::make('purchase_at')
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ManageAssetManagement::route('/'),
        ];
    }
}
