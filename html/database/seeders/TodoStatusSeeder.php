<?php

namespace Database\Seeders;

use App\Models\TodoStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TodoStatusSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TodoStatus::truncate();

        $dado = [
            ['id' => 1, 'description' => __('todo_lang.status.open')],
            ['id' => 2, 'description' => __('todo_lang.status.closed')],
        ];

        TodoStatus::insert($dado);
    }

}
