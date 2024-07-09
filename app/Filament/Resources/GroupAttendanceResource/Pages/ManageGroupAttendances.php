<?php

namespace App\Filament\Resources\GroupAttendanceResource\Pages;

use App\Filament\Resources\GroupAttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Model;

class ManageGroupAttendances extends ManageRecords
{
    protected static string $resource = GroupAttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->using(function (array $data, string $model): Model {
                $q = $model::create([
                    'name'=>$data['name'],
                    'description'=>$data['description']
                ]);
                $q->user()->sync($data['nik']);
                return $q;
            }),
        ];
    }
}
