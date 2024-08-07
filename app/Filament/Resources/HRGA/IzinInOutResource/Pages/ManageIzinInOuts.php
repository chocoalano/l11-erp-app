<?php

namespace App\Filament\Resources\HRGA\IzinInOutResource\Pages;

use App\Filament\Resources\HRGA\IzinInOutResource;
use App\Models\HRGA\Event;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ManageIzinInOuts extends ManageRecords
{
    protected static string $resource = IzinInOutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->using(function (array $data, string $model): Model {
                return DB::transaction(function () use ($model, $data) {
                    $data['user_approved'] = 'y';
                    $i = $model::create($data);
                    // Membuat instance Event setelahnya dan mengasosiasikan dengan IzinInOut
                    $e = Event::create([
                        'model_name' => 'App\Models\HRGA\IzinInOut',
                        'id_form' => $i->id,
                    ]);
                    // Mengupdate IzinInOut dengan event_number dari Event
                    $i->update([
                        'event_number' => $e->event_number
                    ]);
                });
            }),
        ];
    }
}
