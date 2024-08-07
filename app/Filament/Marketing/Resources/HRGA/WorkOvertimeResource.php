<?php

namespace App\Filament\Marketing\Resources\HRGA;

use App\Filament\Marketing\Resources\HRGA\WorkOvertimeResource\Pages;
use App\Filament\Marketing\Resources\HRGA\WorkOvertimeResource\RelationManagers;
use App\Models\HRGA\WorkOvertime;
use App\Models\JobPosition;
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

class WorkOvertimeResource extends Resource
{
    protected static ?string $model = WorkOvertime::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'Form Adm HRGA';
    public static function form(Form $form): Form
    {
        $authUser = User::with('employe')->where('id', auth()->user()->id)->first();
        return $form
            ->schema([
                Forms\Components\Section::make('Adjust Attendance Form In Or Out')
                        ->description('Please create your adjust attendance on this form.')
                        ->schema([
                            Forms\Components\TextInput::make('event_number')
                            ->disabled(),
                            Forms\Components\Select::make('organization_id')
                                ->label('Choose Organization')
                                ->options(Organization::all()->pluck('name', 'id'))
                                ->searchable()
                                ->preload(),
                            Forms\Components\Select::make('job_position_id')
                                ->label('Choose Job Position')
                                ->options(JobPosition::all()->pluck('name', 'id'))
                                ->searchable()
                                ->preload(),
                            Forms\Components\DatePicker::make('date_spl'),
                            Forms\Components\Toggle::make('overtime_day_status')
                                ->inline(false),
                            Forms\Components\DatePicker::make('date_overtime_at'),
                        ])->columns([
                            'sm' => 1,
                            'xl' => 2,
                            '2xl' => 2,
                        ]),
                        Forms\Components\Repeater::make('members')
                            ->schema([
                                Forms\Components\Select::make('user_id')
                                    ->label('Choose User')
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
                                    ->preload()
                                    ->required(),
                            ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $u = User::find(auth()->user()->id);
                if ($u->hasRole(['super_admin', 'Support & Infra IT'])) {
                    return $query;
                }else{
                    $usr = User::with('employe')->where('id', auth()->user()->id)->first();
                    $org = Organization::with('employe')->where('id', $usr->employe->organization_id)->first();
                    $userId = [];
                    foreach ($org->employe as $k) {
                        array_push($userId, $k->user_id);
                    }
                    return $query->whereIn('userid_created', $userId)
                        ->orWhereHas('userMember', function($q) use ($userId){
                            $q->whereIn('user_id', $userId);
                        }); 
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('event_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('userCreated.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_spl')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('organization_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('job_position_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('overtime_day_status')
                    ->boolean(),
                Tables\Columns\TextColumn::make('date_overtime_at')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('admin_approved')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'Waiting' => 'gray',
                    'Yes' => 'success',
                    'No' => 'danger',
                })
                ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('line_approved')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'Waiting' => 'gray',
                    'Yes' => 'success',
                    'No' => 'danger',
                })
                ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('gm_approved')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'Waiting' => 'gray',
                    'Yes' => 'success',
                    'No' => 'danger',
                })
                ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('hrga_approved')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'Waiting' => 'gray',
                    'Yes' => 'success',
                    'No' => 'danger',
                })
                ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('director_approved')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'Waiting' => 'gray',
                    'Yes' => 'success',
                    'No' => 'danger',
                })
                ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('fat_approved')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'Waiting' => 'gray',
                    'Yes' => 'success',
                    'No' => 'danger',
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
            ])
            ->filters([
                DateRangeFilter::make('date_spl'),
                DateRangeFilter::make('date_overtime_at')
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
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
                        ->action(function (array $data, WorkOvertime $record): void {
                            $user = User::with('employe')->find($record->userid_created);
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
                        ->hidden(function (WorkOvertime $record): bool {
                            $user = User::with('employe')->find($record->userid_created);
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
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageWorkOvertimes::route('/'),
        ];
    }
}