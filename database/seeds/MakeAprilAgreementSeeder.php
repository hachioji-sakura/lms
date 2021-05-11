<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Agreement;

class MakeAprilAgreementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function(){
            Agreement::whereNull('end_date')->update(['end_date'=>"2021-04-30"]);
            Agreement::all()->map(function($item){
                return $item->update(['start_date'=>"2021-04-01"]);
            });
        });
    }
}
