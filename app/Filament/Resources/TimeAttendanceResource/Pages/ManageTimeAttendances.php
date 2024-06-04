<?php

namespace App\Filament\Resources\TimeAttendanceResource\Pages;

use App\Filament\Resources\TimeAttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTimeAttendances extends ManageRecords
{
    protected static string $resource = TimeAttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
