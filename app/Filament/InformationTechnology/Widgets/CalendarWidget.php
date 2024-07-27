<?php

namespace App\Filament\InformationTechnology\Widgets;

use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use App\Filament\Resources\EventResource;
use App\Models\ScheduleGroupAttendance;

class CalendarWidget extends FullCalendarWidget
{
    public function fetchEvents(array $fetchInfo): array
    {
        $auth = auth()->user()->id;
        return ScheduleGroupAttendance::query()
            ->whereHas('group_users', function($query) use ($auth){
                $query->where('user_id', $auth);
            })
            ->where('date', '>=', $fetchInfo['start'])
            ->where('date', '<=', $fetchInfo['end'])
            ->get()
            ->map(
                fn (ScheduleGroupAttendance $event) => [
                    'title' => $event->time->type,
                    'start' => $event->date,
                    'end' => $event->date,
                    // 'url' => EventResource::getUrl(name: 'view', parameters: ['record' => $event]),
                    'shouldOpenUrlInNewTab' => true
                ]
            )
            ->all();
    }
}
