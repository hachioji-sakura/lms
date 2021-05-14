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
            //体験フローの契約確認中のものを除いて、すべてをcommitに変更
            $except_ids = Ask::where('type','agreement')->pluck('target_model_id');
            Agreement::whereNotIn($except_ids)->where('status','new')->update(['status' => 'commit']);

            //開始日がないなら4/1にセット
            Agreement::whereNull('start_date')->update(['start_date'=>"2021-04-01"]);
            //存在する契約はすべて4月で終了するようにセット
            Agreement::all()->map(function($item){
                return $item->update(['end_date'=>"2021-04-30"]);
            });
        });
    }
}
