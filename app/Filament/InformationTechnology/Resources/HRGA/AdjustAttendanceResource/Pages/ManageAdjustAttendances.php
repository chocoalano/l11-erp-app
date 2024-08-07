<?php

namespace App\Filament\InformationTechnology\Resources\HRGA\AdjustAttendanceResource\Pages;

use App\Filament\InformationTechnology\Resources\HRGA\AdjustAttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use App\Models\HRGA\Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ManageAdjustAttendances extends ManageRecords
{
    protected static string $resource = AdjustAttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->using(function (array $data, string $model): Model {
                return DB::transaction(function () use ($model, $data) {
                    $data['user_approved'] = 'y';
                    $i = $model::create($data);
                    // Membuat instance Event setelahnya dan mengasosiasikan dengan AdjustAttendance
                    $e = Event::create([
                        'model_name' => 'App\Models\HRGA\AdjustAttendance',
                        'id_form' => $i->id,
                    ]);
                    // Mengupdate AdjustAttendance dengan event_number dari Event
                    $i->update([
                        'event_number' => $e->event_number
                    ]);
                });
            }),
        ];
    }
}
