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
            //TODO:askがない奴はcommitにする処理を入れる
            
            Agreement::whereNull('start_date')->update(['start_date'=>"2021-04-01"]);
            Agreement::all()->map(function($item){
                return $item->update(['end_date'=>"2021-04-30"]);
            });
        });
    }
}
