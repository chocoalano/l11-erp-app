<?php

namespace App\Filament\Pages;

use App\Enums\TaskStatus;
use App\Events\SendNotificationEvent;
use App\Models\Task;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Mokhosh\FilamentKanban\Pages\KanbanBoard;
use Illuminate\Support\Collection;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class TasksKanbanBoard extends KanbanBoard
{
    use HasPageShield;
    protected static ?string $title = 'Ticket Support IT';

    protected static string $view = 'filament-kanban::kanban-board';
 
    protected static string $headerView = 'filament-kanban::kanban-header';
    
    protected static string $recordView = 'filament-kanban::kanban-record';
    
    protected static string $statusView = 'filament-kanban::kanban-status';
    
    protected static string $scriptsView = 'filament-kanban::kanban-scripts';

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
            Select::make('participation')
                ->label('Users Participan')
                ->options(User::all()->pluck('name', 'id'))
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
        $task->team()->sync($data['participation']);
        foreach ($data['participation'] as $key) {
            $recipient = User::find($key);
            Notification::make()
                ->title('Updated task successfully '. $task->title)
                ->sendToDatabase($recipient)
                ->broadcast($recipient)
                ->success()
                ->send();
    
        }
        event(new SendNotificationEvent($task->title));
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
                    Select::make('participation')
                    ->label('Users Participan')
                    ->options(User::all()
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
                    foreach ($data['participation'] as $key) {
                        $recipient = User::find($key);
                        Notification::make()
                            ->title('Create new task successfully '. $u->title)
                            ->sendToDatabase($recipient)
                            ->broadcast($recipient)
                            ->success()
                            ->send();
                
                    }
                    event(new SendNotificationEvent($u->title));
                    return $data;
                })
        ];
    }
}
