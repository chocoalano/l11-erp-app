<?php

namespace App\Filament\InformationTechnology\Resources\HRGA;

use App\Filament\InformationTechnology\Resources\HRGA\CutiResource\Pages;
use App\Filament\InformationTechnology\Resources\HRGA\CutiResource\RelationManagers;
use App\Models\HRGA\Cuti;
use App\Models\Organization;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class CutiResource extends Resource
{
    protected static ?string $model = Cuti::class;

    protected static ?string $navigationIcon = 'heroicon-s-document-currency-rupee';
    protected static ?string $navigationGroup = 'Form Adm HRGA';

    public static function form(Form $form): Form
    {
        $authUser = User::with('employe')->where('id', auth()->user()->id)->first();
        return $form
            ->schema([
                Forms\Components\Section::make('Form Cuti')
                        ->columns([
                            'sm' => 1,
                            'xl' => 2,
                            '2xl' => 2,
                        ])
                        ->schema([
                            Forms\Components\TextInput::make('event_number')
                            ->disabled(),
                            Forms\Components\Select::make('user_id')
                                ->label('Choose Users')
                                ->options(function (Builder $query) use ($authUser) {
                                    $u = User::find(auth()->user()->id);
                                    if ($u->hasRole(['super_admin'])) {
                                        return User::get()
                                            ->pluck('name', 'id');
                                    }else{
                                        return User::with('employe')
                                            ->whereHas('employe', function ($q) use ($authUser) {
                                                $q->where('organization_id', $authUser->employe->organization_id);
                                            })
                                            ->get()
                                            ->pluck('name', 'id');
                                    }
                                })
                                ->searchable()
                                ->required(),
                            Forms\Components\TextInput::make('category')->required(),
                            Forms\Components\DatePicker::make('start_date')->required(),
                            Forms\Components\DatePicker::make('end_date')->required(),
                            Forms\Components\TimePicker::make('start_time')->required(),
                            Forms\Components\TimePicker::make('end_time')->required(),
                            Forms\Components\Textarea::make('description')->columnSpanFull()->required()
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
                    return $query->whereIn('user_id', $userId); 
                } 
            })
            ->columns([
                Tables\Columns\TextColumn::make('event_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_time'),
                Tables\Columns\TextColumn::make('end_time'),
                Tables\Columns\TextColumn::make('category')
                ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('user_approved')
                ->toggleable(isToggledHiddenByDefault: true)
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'Waiting' => 'gray',
                    'Yes' => 'success',
                    'No' => 'danger',
                }),
                Tables\Columns\TextColumn::make('line_approved')
                ->toggleable(isToggledHiddenByDefault: true)
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'Waiting' => 'gray',
                    'Yes' => 'success',
                    'No' => 'danger',
                }),
                Tables\Columns\TextColumn::make('hrga_approved')
                ->toggleable(isToggledHiddenByDefault: true)
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'Waiting' => 'gray',
                    'Yes' => 'success',
                    'No' => 'danger',
                }),
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
                DateRangeFilter::make('start_date'),
                DateRangeFilter::make('end_date')
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                    ->hidden(function (): bool {
                        $u = User::find(auth()->user()->id);
                        return ($u->hasRole(['super_admin', 'Support & Infra IT'])) ? false : true;
                    }),
                    Tables\Actions\EditAction::make()
                    ->hidden(function (): bool {
                        $u = User::find(auth()->user()->id);
                        return ($u->hasRole(['super_admin', 'Support & Infra IT'])) ? false : true;
                    }),
                    Tables\Actions\DeleteAction::make()
                    ->hidden(function (): bool {
                        $u = User::find(auth()->user()->id);
                        return ($u->hasRole(['super_admin', 'Support & Infra IT'])) ? false : true;
                    }),
                    Tables\Actions\Action::make('Approved')
                        ->form([
                            Forms\Components\Radio::make('feedback')
                                ->label('Will you approve or reject this request?')
                                ->options([
                                    'y' => 'Approved',
                                    'n' => 'Rejected'
                                ])
                                ->inline()
                                ->inlineLabel(false),
                        ])
                        ->action(function (array $data, Cuti $record): void {
                            $user = User::with('employe')->find($record->user_id);
                            $emp = $user->employe;

                            $userHr = User::whereHas('employe', function ($q) {
                                $q->where('organization_id', 11)
                                ->where('job_position_id', 15);
                            })->first();
                            if(auth()->user()->id === $emp->approval_manager){
                                $data['line_approved'] = $data['feedback'];
                            }
                            if(auth()->user()->id === $userHr->id){
                                $data['hrga_approved'] = $data['feedback'];
                            }
                            $record->update($data);
                        })
                        ->modalWidth(MaxWidth::Small)
                        ->icon('fas-signature')
                        ->hidden(function (Cuti $record): bool {
                            $user = User::with('employe')->find($record->user_id);
                            $emp = $user->employe;

                            $userHr = User::whereHas('employe', function ($q) {
                                $q->where('organization_id', 11)
                                ->where('job_position_id', 15);
                            })->first();

                            $userAuthorize = [
                                $emp->approval_line,
                                $emp->approval_manager,
                                $userHr->id,
                            ];
                            return (in_array(auth()->user()->id, $userAuthorize)) ? false : true;
                        })
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
            'index' => Pages\ManageCutis::route('/'),
        ];
    }
}
