<?php

use Illuminate\Database\Seeder;
use App\Models\Ask;

class MoveAccessKeyUserToAskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //依頼のアクセスキーをuserからaskに移設
        //アクセスキーの修正リリース時のみ使用
        //対象依頼を取得
        $target_asks = Ask::whereIn('type',["agreement","hope_to_join"])
                      ->whereNotIn('status',["cancel","commit"])
                      ->whereDate("created_at",">=",date("Y-m-d",strtotime('previous month')))
                      ->get();

        $target_asks->map(function($item){
          $item->access_key = $item->target_user->access_key;
          return $item->save();
        });
    }
}
