<?php

namespace App\Filament\Resources\SystemSetup\GradeEducationResource\Pages;

use App\Filament\Resources\SystemSetup\GradeEducationResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageGradeEducation extends ManageRecords
{
    protected static string $resource = GradeEducationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
