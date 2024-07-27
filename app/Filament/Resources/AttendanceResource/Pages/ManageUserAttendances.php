<?php

namespace App\Filament\Resources\UserAttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use Filament\Resources\Pages\ManageRecords;

class ManageUserAttendances extends ManageRecords
{
    protected static string $resource = AttendanceResource::class;
}
