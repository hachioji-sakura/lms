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
        $this->call(GeneralAttributesSeeder::class);
        $this->call(PlaceSeeder::class);
        $this->call(ImagesTableSeeder::class);
        $this->call(TextbookSeeder::class);
        $this->call(ManagersTableSeeder::class);
        $this->call(UserCalendarSeeder::class);
        $this->call(StudentGroupSeeder::class);
    }
}
