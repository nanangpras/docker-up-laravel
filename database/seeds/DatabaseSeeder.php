<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);

        $basic_data = database_path('sql/data_eba.sql');
        DB::unprepared(file_get_contents($basic_data));
        $this->command->info('Basic table seeded!');
    }
}
