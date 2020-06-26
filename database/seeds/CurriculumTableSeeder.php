<?php

use Illuminate\Database\Seeder;
use App\Models\Curriculum;
use App\Models\Subject;

class CurriculumTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $jh_math_id = Subject::where('name','数学(中学)')->first()->id;
        $jh_science_id = Subject::where('name','理科(中学)')->first()->id;
        $jh_japanese_id = Subject::where('name','国語(中学)')->first()->id;
        $h_math_I_id = Subject::where('name','数ⅠA')->first()->id;
        $h_math_II_id = Subject::where('name','数ⅡB')->first()->id;
        $h_math_III_id = Subject::where('name','数Ⅲ')->first()->id;
        $h_physical_id = Subject::where('name','物理')->first()->id;
        $h_chemistry_id = Subject::where('name','化学')->first()->id;


        $datam  = [
          $jh_math_id => [
            '正負の数',
            '加法　減法',
            '乗法　除法',
            '四則の計算',
            '文字式の計算',
            '関数',
            '比例　反比例',
            '比例のグラフ',
            '反比例のグラフ',
            '等式の変形',
            '文字式の利用',
            '乗法公式',
            '因数分解',
            'ルートの計算',
            '一次方程式',
            '連立方程式',
            '二次方程式',
            '確率',
            '資料の活用',
            '円と角度',
            '作図',
            '文字式の証明',
            '一次関数',
            '一次関数のグラフ',
            '変化の割合',
            '二次関数',
            '二次関数のグラフ',
            '関数と方程式',
            '平面図形の角',
            '三角形の合同',
            '合同の証明',
            '三角形の相似',
            '相似の証明',
            '三平方の定理',
            '平面図形の面積',
            '空間図形の体積',
          ],

          $jh_science_id => [
            '植物',
            '動物',
            '細胞　遺伝',
            '自然環境',
            '大地',
            '天気',
            '天体',
            '物質',
            '光　音',
            '力',
            '運動',
            'エネルギー',
            '電気',
            '化学変化',
            'イオン',
          ],

          $jh_japanese_id => [
            '漢字　語句',
            '作文',
            '発表文',
            '報告文',
            '記録文',
            '案内文',
            '小説',
            '随筆',
            '論説文',
            '古文',
            '漢文',
            '文　文節　単語',
            '自立語　付属語',
            '動詞　形容詞　名詞',
            '助詞　助動詞',
          ],

          $h_math_I_id => [
            '式の計算　実数',
            '１次不等式',
            '集合　命題',
            '２次関数',
            '２次不等式',
            '三角比',
            'データの分析',
            '場合の数　確率',
            '図形の性質',
            '整数の性質',
            'ユークリッドの互除法',
            '１次不定方程式',
            'ｎ 進法',
            '式と証明',
          ],

          $h_math_II_id => [
            '複素数',
            '高次方程式',
            '直線・円の方程式',
            '軌跡　領域',
            '三角関数',
            '指数関数',
            '対数関数',
            '微分法',
            '積分法',
            'ベクトル',
            '数列',
            '確率分布',
          ],

          $h_math_III_id => [
            '複素数平面',
            '２次曲線',
            '媒介変数',
            '極座標　極方程式',
            '分数関数',
            '無理関数',
            '数列の極限',
            '関数の極限',
          ],

          $h_physical_id => [
            '運動',
            '力学',
            '熱力学',
            '波動',
            '電磁気',
            'コンデンサー',
            '交流回路',
            '量子力学',
          ],

          $h_chemistry_id => [
            'mol 計算',
            '溶解度',
            '電気分解',
            '反応速度',
            '化学平衡',
          ],
        ];
        $i = 1;
        foreach($datam as $key => $value){
          foreach($value as $name){
            $form = [
              'name' => $name,
              'create_user_id' => 1,
              'sort_no' => $i,
            ];
            $item = Curriculum::create($form);
            $item->subjects()->attach($key);
            $i++;
          }
        }
    }
}
