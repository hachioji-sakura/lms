<?php

use Illuminate\Database\Seeder;
use App\Models\Publisher;
use App\Models\Textbook;
use App\Models\TextbookChapter;
use App\Models\TextbookQuestion;
use App\Models\TextbookAnswer;


class TextbookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

      $_publisher = Publisher::create([
        'name' => '八王子サクラ',
        'url' => 'http://hachiojisakura.com',
        'create_user_id' => 1,
        ]);
      $_book = Textbook::create([
        'name' => 'サンプル問題集',
        'explain' => 'テスト用',
        'publisher_id' => $_publisher->id,
        'create_user_id' => 1,
        ]);
      $_chapter = TextbookChapter::create([
        'textbook_id' => $_book->id,
        'title' => 'たしざん',
        'body' => '',
        'sort_no' => 1,
        ]);
      $_templates = [
        ['q' => '１＋１＝', 'a' => [2,'２']],
        ['q' => '１＋２＝', 'a' => [3,'３']],
        ['q' => '２＋１＝', 'a' => [3,'３']],
        ['q' => '２＋２＝', 'a' => [4,'４']],
        ['q' => '２＋３＝', 'a' => [5,'５']],
      ];
      $c = 0;
      foreach($_templates as $_template){
        $c++;
        $_question = TextbookQuestion::create([
          'chapter_id' => $_chapter->id,
          'title' => 'つぎのけいさんをしてみよう',
          'body' => $_template['q'],
          'score' => 20
        ]);
        foreach($_template['a'] as $_answer){
          $_a = TextbookAnswer::create([
            'question_id' => $_question->id,
            'sort_no' => $c,
            'answer_text' => $_answer,
          ]);
        }
      }
      $_chapter = TextbookChapter::create([
        'textbook_id' => $_book->id,
        'title' => 'かけざん',
        'body' => '',
        'sort_no' => 1,
        ]);
      $_templates = [
        ['q' => '２×１＝', 'a' => [2,'２']],
        ['q' => '２×２＝', 'a' => [4,'４']],
        ['q' => '２×３＝', 'a' => [6,'６']],
        ['q' => '２×４＝', 'a' => [8,'８']],
        ['q' => '２×５＝', 'a' => [10,'１０']],
      ];
      $c = 0;
      foreach($_templates as $_template){
        $c++;
        $_question = TextbookQuestion::create([
          'chapter_id' => $_chapter->id,
          'title' => 'つぎのけいさんをしてみよう',
          'body' => $_template['q'],
          'score' => 20
        ]);
        foreach($_template['a'] as $_answer){
          $_a = TextbookAnswer::create([
            'question_id' => $_question->id,
            'sort_no' => $c,
            'answer_text' => $_answer,
          ]);
        }
      }
      $_publisher = Publisher::create([
        'name' => 'アルク',
        'url' => 'https://www.alc.co.jp/',
        'create_user_id' => 1,
        ]);
      $_book = Textbook::create([
        'name' => '夢をかなえる英単語 新ユメタン0 中学修了〜高校基礎レベル (英語の超人になる!アルク学参シリーズ)',
        'explain' => '',
        'publisher_id' => $_publisher->id,
        'create_user_id' => 1,
        ]);

    }
}
