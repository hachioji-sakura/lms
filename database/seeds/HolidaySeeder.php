<?php

use Illuminate\Database\Seeder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Holiday;

class HolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      Holiday::holiday_update();
    }

}
