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
    $publishers = config('publisher');
    foreach($publishers as $publisher){
      Publisher::create([
        'name' => $publisher['name'],
        'url' => $publisher['url'],
        'create_user_id' => 1,
      ]);
    }

    //発注先
    $suppliers = config('supplier');
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
    $grades = [];
    $subjects =[];
    foreach($data as $key => $datum) {
      $level = $datum[0]->level;
      $explain = $datum[0]->explain;
      $teikaPrice = $datum[0]->teika_price;
      $tewatashiPrice1 = $datum[0]->tewatashi_price1;
      $tewatashiPrice2 = $datum[0]->tewatashi_price2;
      $tewatashiPrice3 = $datum[0]->tewatashi_price3;
      $publisherPrice = $datum[0]->publisher_price;
      foreach ($datum as $value) {
        if (isset($value->subject)) $items['subjects'][] = $value->subject;
        if (isset($value->grade)) $items['grades'][] = $value->grade;
        $publisher = Publisher::where('name', $value->publisher_name)->first();
        $supplier = Supplier::where('name', $value->supplier_name)->first();
      }

      if (isset($items['subjects'])) $subjects = array_unique($items['subjects']);
      if (isset($items['grades'])) $grades = array_unique($items['grades']);
      //教材
      $textbook = Textbook::create([
        'name' => $datum[0]->name,
        'explain' => $explain ?? '',
        'difficulty' => $level??null,
        'publisher_id' => $publisher->id ?? null,
        'supplier_id' => $supplier->id ?? null,
        'create_user_id' => 1,
      ]);
      if(!empty($teikaPrice)) $this->create_price_tag($textbook->id,'teika_price',$teikaPrice);
      if(!empty($tewatashiPrice1)) $this->create_price_tag($textbook->id,'selling_price',$tewatashiPrice1);
      if(!empty($tewatashiPrice2)) $this->create_price_tag($textbook->id,'amazon_price',$tewatashiPrice2);
      if(!empty($tewatashiPrice3)) $this->create_price_tag($textbook->id,'other_price',$tewatashiPrice3);
      if(!empty($publisherPrice)) $this->create_price_tag($textbook->id,'publisher_price',$publisherPrice);
      //教科
      foreach($subjects as $subject){
        $subjectEloquent = Subject::where('name','=',$subject)->first();
        if(!isset($subjectEloquent)){
          $maxSubjectId = Subject::max('id');
          Subject::create([
            'name' => $subject,
            'sort_no' => $maxSubjectId + 1,
            'create_user_id' => 1,
          ]);
        }
        $subjectModel = Subject::where('name', '=', $subject)->first();
        TextbookSubject::create([
          'textbook_id' => $textbook->id,
          'subject_id' => $subjectModel->id,
        ]);
      }
      //学年
      foreach($grades as $grade){
        $generalAttribute = GeneralAttribute::where('attribute_key','grade')
                                            ->where('attribute_name',$grade)
                                            ->first();
        if(isset($generalAttribute)) {
          TextbookTag::create([
            'textbook_id' => $textbook->id,
            'tag_key' => 'grade_no',
            'tag_value' => $generalAttribute->id,
            'create_user_id' => 1,
          ]);
        }
      }
    }
  }

  /**
   * 教科と教材のリレーション登録
   * @param int $textbookId
   * @param string $key
   * @param int $value
   */
  private function create_price_tag($textbookId,$key,$value){
    TextbookTag::create([
      'textbook_id' => $textbookId,
      'tag_key' => $key,
      'tag_value' =>$value,
      'create_user_id' => 1,
    ]);
  }
}
