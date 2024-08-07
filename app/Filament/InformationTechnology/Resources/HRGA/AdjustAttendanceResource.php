<?php

namespace App\Filament\InformationTechnology\Resources\HRGA;

use App\Filament\InformationTechnology\Resources\HRGA\AdjustAttendanceResource\Pages;
use App\Filament\InformationTechnology\Resources\HRGA\AdjustAttendanceResource\RelationManagers;
use App\Models\HRGA\AdjustAttendance;
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

class AdjustAttendanceResource extends Resource
{
    protected static ?string $model = AdjustAttendance::class;

    protected static ?string $navigationIcon = 'heroicon-c-rocket-launch';
    protected static ?string $navigationGroup = 'Form Adm HRGA';

    public static function form(Form $form): Form
    {
        $authUser = User::with('employe')->where('id', auth()->user()->id)->first();
        return $form
            ->schema([
                Forms\Components\Section::make('Adjust Attendance Form In Or Out')
                    ->description('Please create your adjust attendance on this form.')
                    ->schema([
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
                            ->preload(),
                        Forms\Components\TextInput::make('problem'),
                        Forms\Components\DatePicker::make('date'),
                        Forms\Components\Textarea::make('description')
                        ->columnSpanFull()
                    ])->columns([
                        'sm' => 1,
                        'xl' => 2,
                        '2xl' => 2,
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
                Tables\Columns\TextColumn::make('problem')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_approved')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'Waiting' => 'gray',
                    'Yes' => 'success',
                    'No' => 'danger',
                }),
                Tables\Columns\TextColumn::make('line_approved')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'Waiting' => 'gray',
                    'Yes' => 'success',
                    'No' => 'danger',
                }),
                Tables\Columns\TextColumn::make('hrga_approved')
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
                DateRangeFilter::make('date')
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
                        ->action(function (array $data, AdjustAttendance $record): void {
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
                        ->hidden(function (AdjustAttendance $record): bool {
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
            'index' => Pages\ManageAdjustAttendances::route('/'),
        ];
    }
}
