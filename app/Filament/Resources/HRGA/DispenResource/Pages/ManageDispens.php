<?php

namespace App\Filament\Resources\HRGA\DispenResource\Pages;

use App\Filament\Resources\HRGA\DispenResource;
use App\Models\HRGA\Event;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ManageDispens extends ManageRecords
{
    protected static string $resource = DispenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->using(function (array $data, string $model): Model {
                return DB::transaction(function () use ($model, $data) {
                    $data['user_approved'] = 'y';
                    $i = $model::create($data);
                    // Membuat instance Event setelahnya dan mengasosiasikan dengan Dispen
                    $e = Event::create([
                        'model_name' => 'App\Models\HRGA\Dispen',
                        'id_form' => $i->id,
                    ]);
                    // Mengupdate Dispen dengan event_number dari Event
                    $i->update([
                        'event_number' => $e->event_number
                    ]);
                });
            }),
        ];
    }
}
