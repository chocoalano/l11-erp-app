<?php

namespace App\Filament\InformationTechnology\Pages;

use App\Enums\TaskStatus;
use App\Models\Task;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\Facades\DB;
use Mokhosh\FilamentKanban\Pages\KanbanBoard;
use Illuminate\Support\Collection;

class Tasks extends KanbanBoard
{
    protected static ?string $title = 'Tasks';
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static string $headerView = 'tasks-kanban.kanban-header';

    protected static string $recordView = 'tasks-kanban.kanban-record';

    protected static string $statusView = 'tasks-kanban.kanban-status';

    protected static string $model = Task::class;

    protected static string $statusEnum = TaskStatus::class;

    protected function records(): Collection
    {
        return 
        Task::where('user_id', auth()->id())
        ->where('progress', '<', 100)
        ->orWhereHas('team', function ($query) {
            $query->where('user_id', auth()->id());
        })
        ->get();
    }

    protected function getEditModalFormSchema(null|int $recordId): array
    {
        return [
            TextInput::make('title'),
            Textarea::make('description'),
            Toggle::make('urgent'),
            TextInput::make('progress')->numeric(),
            Select::make('status')
            ->options([
                'Low' => 'Low',
                'High' => 'High',
                'Medium' => 'Medium',
            ]),
            Select::make('user_id')
                ->label('Users Participan')
                ->options(\App\Models\User::all()->pluck('name', 'id'))
                ->multiple()
                ->preload()
                ->searchable()
                ->required()
        ];
    }

    protected function editRecord($recordId, array $data, array $state): void
    {
        $task = Task::find($recordId);
        $task->update([
            'title'=>$data['title'],
            'description'=>$data['description'],
            'urgent'=>$data['urgent'],
            'progress'=>$data['progress'],
            'status'=>$data['status'],
        ]);
        $task->team()->sync($data['user_id']);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->model(Task::class)
                ->form([
                    TextInput::make('title'),
                    Textarea::make('description'),
                    Toggle::make('urgent'),
                    TextInput::make('progress')->numeric(),
                    Select::make('status')
                    ->options([
                        'Low' => 'Low',
                        'High' => 'High',
                        'Medium' => 'Medium',
                    ]),
                    Select::make('user_id')
                    ->label('Users Participan')
                    ->options(\App\Models\User::all()
                    ->pluck('name', 'id'))
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->required()
                ])
                ->mutateFormDataUsing(function ($data) {
                    $u=new Task();
                    $u->title=$data['title'];
                    $u->description=$data['description'];
                    $u->urgent=$data['urgent'];
                    $u->progress=$data['progress'];
                    $u->status=$data['status'];
                    $u->user_id=auth()->id();
                    $u->save();
                    $u->team()->sync($data['user_id']);

                    $data['user_id']=auth()->id();
                    return $data;
                })
        ];
    }
}
