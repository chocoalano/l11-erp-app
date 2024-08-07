<?php

namespace App\Filament\Marketing\Resources\HRGA\WorkOvertimeResource\Pages;

use App\Filament\Marketing\Resources\HRGA\WorkOvertimeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use App\Models\HRGA\Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ManageWorkOvertimes extends ManageRecords
{
    protected static string $resource = WorkOvertimeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->using(function (array $data, string $model): Model {
                return DB::transaction(function () use ($model, $data) {
                    $data['user_approved'] = 'y';
                    $i = $model::create($data);
                    // Membuat instance Event setelahnya dan mengasosiasikan dengan WorkOvertime
                    $e = Event::create([
                        'model_name' => 'App\Models\HRGA\WorkOvertime',
                        'id_form' => $i->id,
                    ]);
                    // Mengupdate WorkOvertime dengan event_number dari Event
                    $i->update([
                        'event_number' => $e->event_number
                    ]);
                });
            }),
        ];
    }
}
