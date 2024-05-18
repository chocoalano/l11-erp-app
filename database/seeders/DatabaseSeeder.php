<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Task;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        $users = User::factory(10)->create();
        $tasks = Task::factory(30)
             ->recycle($users)
             ->create();

         $tasks->each(function (Task $task) use ($users) {
             $task->team()->attach(
                 $users->shuffle()
                     ->take(fake()->numberBetween(1, 4))
                     ->pluck('id')
             );
         });
    }
}
