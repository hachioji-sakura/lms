<?php

use App\Models\GeneralAttribute;
use App\Models\Subject;
use App\Models\Supplier;
use App\Models\TextbookSubject;
use App\Models\TextbookTag;
use Illuminate\Database\Seeder;
use App\Models\Publisher;
use App\Models\Textbook;

class TextbookSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    DB::table('publishers')->truncate();
    DB::table('suppliers')->truncate();
    DB::table('textbooks')->truncate();
    DB::table('textbook_tags')->truncate();
    DB::table('textbook_chapters')->truncate();
    DB::table('textbook_questions')->truncate();
    DB::table('textbook_answers')->truncate();
    DB::table('textbook_subjects')->truncate();

    //出版社
    $publishers= array(
      1 => [
        'name' => '日本教材出版',
        'url' => 'https://www.nihonkyouzai.jp',
      ],
      2 => [
        'name' => '育伸社',
        'url' => 'https://www.ikushin.co.jp/',
      ],
      3 => [
        'name' => '好学出版',
        'url' => 'http://www.kogaku-pub.com/',
      ],
      4 => [
        'name' => '教育開発出版',
        'url' => 'https://www.kyo-kai.co.jp/',
      ],
      5 => [
        'name' => '学書',
        'url' => 'https://www.gakusho.com/',
      ],
      6 => [
        'name' => 'エデュケーショナル ネットワーク',
        'url' => 'https://www.edu-network.jp/',
      ],
      7 => [
        'name' => '四谷大塚',
        'url' => 'https://www.yotsuyaotsuka.com/',
      ],
      8 => [
        'name' => '進学研究会',
        'url' => 'https://www.shinken.co.jp/',
      ],
      9 => [
        'name' => '旺文社',
        'url' => 'https://www.obunsha.co.jp/',
      ],
      10 => [
        'name' => '代々木ライブラリー ',
        'url' => 'https://www.yozemi-sateline.ac/library/',
      ],
      11 => [
        'name' => '学研',
        'url' => 'https://hon.gakken.jp/',
      ],
      12 => [
        'name' => '学研プラス',
        'url' => 'https://gakken-plus.co.jp/',
      ],
      13 => [
        'name' => '日本英語検定協会',
        'url' => 'https://www.eiken.or.jp/',
      ],
    );

    foreach($publishers as $publisher){
      Publisher::create([
        'name' => $publisher['name'],
        'url' => $publisher['url'],
        'create_user_id' => 1,
      ]);
    }

    //発注先
    $suppliers = array(
      1 => [
        'name' => '日本教材出版',
        'url' => 'https://www.nihonkyouzai.jp',
      ],
      2 => [
        'name' => '育伸社',
        'url' => 'https://www.ikushin.co.jp/',
      ],
      3 => [
        'name' => 'amazon',
        'url' => 'https://www.amazon.co.jp/',
      ],
      4 => [
        'name' => '教育開発出版',
        'url' => 'https://www.kyo-kai.co.jp/',
      ],
      5 => [
        'name' => '四谷大塚',
        'url' => 'https://www.yotsuyaotsuka.com/',
      ],
      6 => [
        'name' => '暁出版',
        'url' => 'https://www.akatsuki-shuppan.co.jp/',
      ],
      7 => [
        'name' => '楽天',
        'url' => 'https://www.rakuten.co.jp/',
      ],
      8 => [
        'name' => '進学研究会',
        'url' => 'https://www.shinken.co.jp/',
      ],
      9 => [
        'name' => 'エデュケーショナル ネットワーク',
        'url' => 'https://www.edu-network.jp/',
      ],
      10 => [
        'name' => 'ELTBOOKS',
        'url' => 'https://www.eltbooks.com/home.php',
      ],
      11 => [
        'name' => 'くまざわ書店 ',
        'url' => 'https://www.kumabook.com/',
      ],
      12 => [
        'name' => '日本英語検定協会',
        'url' => 'https://www.eiken.or.jp/',
      ],
    );
    foreach($suppliers as $supplier){
      Supplier::create([
        'name' => $supplier['name'],
        'url' => $supplier['url'],
        'create_user_id' => 1,
      ]);
    }

    //教材 (sakura-api)
    $ctx = stream_context_create(array(
      "http" => array(
        "method" => "GET",
        "header" => "User-Agent: Mozilla/5.0 (Windows NT 6.3; WOW64; Trident/7.0; Touch; rv:11.0) like Gecko \r\n"
                   ."Api-Token: 7511a32c7b6fd3d085f7c6cbe66049e7\r\n",
        "ssl" => array(
          "verify_peer"      => false,
          "verify_peer_name" => false
        )
      )
    )
    );
    $res = file_get_contents('https://management.sakuraone.jp/sakura-api/api_get_material.php',false,$ctx);
    $res = json_decode($res);

    $data = [];
    foreach($res->data as $d){
      $data[$d->id][] = $d;
    }

    $items = [];
    foreach($data as $key => $datum) {
      $level = $datum[0]->level;
      $explain = $datum[0]->explain;
      $teika_price = $datum[0]->teika_price;
      $tewatashi_price1 = $datum[0]->tewatashi_price1;
      $tewatashi_price2 = $datum[0]->tewatashi_price2;
      $tewatashi_price3 = $datum[0]->tewatashi_price3;
      $publisher_price = $datum[0]->publisher_price;

      $subjects = [];
      $grades = [];
      foreach ($datum as $value) {
        if (isset($value->subject)) $items[$key]['subjects'][] = $value->subject;
        if (isset($value->grade)) $items[$key]['grades'][] = $value->grade;
        $publisher = Publisher::where('name', $value->publisher_name)->first();
        $supplier = Supplier::where('name', $value->supplier_name)->first();
      }
      $remark = '';
      if (isset($items[$key]['subjects'])){
        $subjects = array_unique($items[$key]['subjects']);
        foreach($subjects as $subject){
          $remark =  $remark.$subject.',';
        }
        $remark = mb_substr($remark, 0, -1);
      }
      if (isset($items[$key]['grades'])) $grades = array_unique($items[$key]['grades']);

      //教材
      $textbook = Textbook::create([
        'name' => $datum[0]->name,
        'explain' => $explain??'',
        'difficulty' => $level??0,
        'publisher_id' => $publisher->id ?? null,
        'supplier_id' => $supplier->id ?? null,
        'create_user_id' => 1,
        'remarks' => $remark,
      ]);
      if(!empty($teikaPrice)) {
        $textbook->textbook_tags()->create([
          'tag_key' => 'teika_price',
          'tag_value' => $teika_price,
          'create_user_id' => 1]);
        }
      if(!empty($tewatashi_price1)) {
        $textbook->textbook_tags()->create([
          'tag_key' => 'selling_price',
          'tag_value' => $tewatashi_price1,
          'create_user_id' => 1]);
      }
      if(!empty($tewatashi_price2)) {
        $textbook->textbook_tags()->create([
          'tag_key' => 'amazon_price',
          'tag_value' => $tewatashi_price2,
          'create_user_id' => 1]);
      }
      if(!empty($tewatashi_price3)) {
        $textbook->textbook_tags()->create([
          'tag_key' => 'other_price',
          'tag_value' => $tewatashi_price3,
          'create_user_id' => 1]);
      }
      if(!empty($publisher_price)) {
        $textbook->textbook_tags()->create([
          'tag_key' => 'publisher_price',
          'tag_value' => $publisher_price,
          'create_user_id' => 1]);
      }

      //教科
      foreach($grades as $grade){
        $general_attribute = GeneralAttribute::where('attribute_key','grade')
                                            ->where('attribute_name',$grade)
                                            ->first();
        if(isset($general_attribute)) {
          TextbookTag::create([
            'textbook_id' => $textbook->id,
            'tag_key' => 'grade',
            'tag_value' => $general_attribute->attribute_value,
            'create_user_id' => 1,
          ]);
        }
      }
    }
  }
}
