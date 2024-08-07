<?php

namespace App\Filament\Marketing\Resources\HRGA\CutiResource\Pages;

use App\Filament\Marketing\Resources\HRGA\CutiResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use App\Models\HRGA\Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ManageCutis extends ManageRecords
{
    protected static string $resource = CutiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->using(function (array $data, string $model): Model {
                return DB::transaction(function () use ($model, $data) {
                    $data['user_approved'] = 'y';
                    $i = $model::create($data);
                    // Membuat instance Event setelahnya dan mengasosiasikan dengan Cuti
                    $e = Event::create([
                        'model_name' => 'App\Models\HRGA\Cuti',
                        'id_form' => $i->id,
                    ]);
                    // Mengupdate Cuti dengan event_number dari Event
                    $i->update([
                        'event_number' => $e->event_number
                    ]);
                });
            }),
        ];
    }
}
