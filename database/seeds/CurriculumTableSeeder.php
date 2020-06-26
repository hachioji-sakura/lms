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
          ['curriculum' => [ 'name' => '正負の数','create_user_id' => 1 ],'subject_id' => $jh_math_id],
          ['curriculum' => [ 'name' => '加法　減法','create_user_id' => 1 ],'subject_id' => $jh_math_id],
          ['curriculum' => [ 'name' => '乗法　除法','create_user_id' => 1 ],'subject_id' => $jh_math_id],
          ['curriculum' => [ 'name' => '四則の計算','create_user_id' => 1 ],'subject_id' => $jh_math_id],
          ['curriculum' => [ 'name' => '文字式の計算','create_user_id' => 1 ],'subject_id' => $jh_math_id],
          ['curriculum' => [ 'name' => '関数','create_user_id' => 1 ],'subject_id' => $jh_math_id],
          ['curriculum' => [ 'name' => '比例　反比例','create_user_id' => 1 ],'subject_id' => $jh_math_id],
          ['curriculum' => [ 'name' => '比例のグラフ','create_user_id' => 1 ],'subject_id' => $jh_math_id],
          ['curriculum' => [ 'name' => '反比例のグラフ','create_user_id' => 1 ],'subject_id' => $jh_math_id],
          ['curriculum' => [ 'name' => '等式の変形','create_user_id' => 1 ],'subject_id' => $jh_math_id],
          ['curriculum' => [ 'name' => '文字式の利用','create_user_id' => 1 ],'subject_id' => $jh_math_id],
          ['curriculum' => [ 'name' => '乗法公式','create_user_id' => 1 ],'subject_id' => $jh_math_id],
          ['curriculum' => [ 'name' => '因数分解','create_user_id' => 1 ],'subject_id' => $jh_math_id],
          ['curriculum' => [ 'name' => 'ルートの計算','create_user_id' => 1 ],'subject_id' => $jh_math_id],
          ['curriculum' => [ 'name' => '一次方程式','create_user_id' => 1 ],'subject_id' => $jh_math_id],
          ['curriculum' => [ 'name' => '連立方程式','create_user_id' => 1 ],'subject_id' => $jh_math_id],
          ['curriculum' => [ 'name' => '二次方程式','create_user_id' => 1 ],'subject_id' => $jh_math_id],
          ['curriculum' => [ 'name' => '確率','create_user_id' => 1 ],'subject_id' => $jh_math_id],
          ['curriculum' => [ 'name' => '資料の活用','create_user_id' => 1 ],'subject_id' => $jh_math_id],
          ['curriculum' => [ 'name' => '円と角度','create_user_id' => 1 ],'subject_id' => $jh_math_id],
          ['curriculum' => [ 'name' => '作図','create_user_id' => 1 ],'subject_id' => $jh_math_id],
          ['curriculum' => [ 'name' => '文字式の証明','create_user_id' => 1 ],'subject_id' => $jh_math_id],
          ['curriculum' => [ 'name' => '一次関数','create_user_id' => 1 ],'subject_id' => $jh_math_id],
          ['curriculum' => [ 'name' => '一次関数のグラフ','create_user_id' => 1 ],'subject_id' => $jh_math_id],
          ['curriculum' => [ 'name' => '変化の割合','create_user_id' => 1 ],'subject_id' => $jh_math_id],
          ['curriculum' => [ 'name' => '二次関数','create_user_id' => 1 ],'subject_id' => $jh_math_id],
          ['curriculum' => [ 'name' => '二次関数のグラフ','create_user_id' => 1 ],'subject_id' => $jh_math_id],
          ['curriculum' => [ 'name' => '関数と方程式','create_user_id' => 1 ],'subject_id' => $jh_math_id],
          ['curriculum' => [ 'name' => '平面図形の角','create_user_id' => 1 ],'subject_id' => $jh_math_id],
          ['curriculum' => [ 'name' => '三角形の合同','create_user_id' => 1 ],'subject_id' => $jh_math_id],
          ['curriculum' => [ 'name' => '合同の証明','create_user_id' => 1 ],'subject_id' => $jh_math_id],
          ['curriculum' => [ 'name' => '三角形の相似','create_user_id' => 1 ],'subject_id' => $jh_math_id],
          ['curriculum' => [ 'name' => '相似の証明','create_user_id' => 1 ],'subject_id' => $jh_math_id],
          ['curriculum' => [ 'name' => '三平方の定理','create_user_id' => 1 ],'subject_id' => $jh_math_id],
          ['curriculum' => [ 'name' => '平面図形の面積','create_user_id' => 1 ],'subject_id' => $jh_math_id],
          ['curriculum' => [ 'name' => '空間図形の体積','create_user_id' => 1 ],'subject_id' => $jh_math_id],
          ['curriculum' => [ 'name' => '植物','create_user_id' => 1 ],'subject_id' => $jh_science_id],
          ['curriculum' => [ 'name' => '動物','create_user_id' => 1 ],'subject_id' => $jh_science_id],
          ['curriculum' => [ 'name' => '細胞　遺伝','create_user_id' => 1 ],'subject_id' => $jh_science_id],
          ['curriculum' => [ 'name' => '自然環境','create_user_id' => 1 ],'subject_id' => $jh_science_id],
          ['curriculum' => [ 'name' => '大地','create_user_id' => 1 ],'subject_id' => $jh_science_id],
          ['curriculum' => [ 'name' => '天気','create_user_id' => 1 ],'subject_id' => $jh_science_id],
          ['curriculum' => [ 'name' => '天体','create_user_id' => 1 ],'subject_id' => $jh_science_id],
          ['curriculum' => [ 'name' => '物質','create_user_id' => 1 ],'subject_id' => $jh_science_id],
          ['curriculum' => [ 'name' => '光　音','create_user_id' => 1 ],'subject_id' => $jh_science_id],
          ['curriculum' => [ 'name' => '力','create_user_id' => 1 ],'subject_id' => $jh_science_id],
          ['curriculum' => [ 'name' => '運動','create_user_id' => 1 ],'subject_id' => $jh_science_id],
          ['curriculum' => [ 'name' => 'エネルギー','create_user_id' => 1 ],'subject_id' => $jh_science_id],
          ['curriculum' => [ 'name' => '電気','create_user_id' => 1 ],'subject_id' => $jh_science_id],
          ['curriculum' => [ 'name' => '化学変化','create_user_id' => 1 ],'subject_id' => $jh_science_id],
          ['curriculum' => [ 'name' => 'イオン','create_user_id' => 1 ],'subject_id' => $jh_science_id],
          ['curriculum' => [ 'name' => '漢字　語句','create_user_id' => 1 ],'subject_id' => $jh_japanese_id],
          ['curriculum' => [ 'name' => '作文','create_user_id' => 1 ],'subject_id' => $jh_japanese_id],
          ['curriculum' => [ 'name' => '発表文','create_user_id' => 1 ],'subject_id' => $jh_japanese_id],
          ['curriculum' => [ 'name' => '報告文','create_user_id' => 1 ],'subject_id' => $jh_japanese_id],
          ['curriculum' => [ 'name' => '記録文','create_user_id' => 1 ],'subject_id' => $jh_japanese_id],
          ['curriculum' => [ 'name' => '案内文','create_user_id' => 1 ],'subject_id' => $jh_japanese_id],
          ['curriculum' => [ 'name' => '小説','create_user_id' => 1 ],'subject_id' => $jh_japanese_id],
          ['curriculum' => [ 'name' => '随筆','create_user_id' => 1 ],'subject_id' => $jh_japanese_id],
          ['curriculum' => [ 'name' => '論説文','create_user_id' => 1 ],'subject_id' => $jh_japanese_id],
          ['curriculum' => [ 'name' => '古文','create_user_id' => 1 ],'subject_id' => $jh_japanese_id],
          ['curriculum' => [ 'name' => '漢文','create_user_id' => 1 ],'subject_id' => $jh_japanese_id],
          ['curriculum' => [ 'name' => '文　文節　単語','create_user_id' => 1 ],'subject_id' => $jh_japanese_id],
          ['curriculum' => [ 'name' => '自立語　付属語','create_user_id' => 1 ],'subject_id' => $jh_japanese_id],
          ['curriculum' => [ 'name' => '動詞　形容詞　名詞','create_user_id' => 1 ],'subject_id' => $jh_japanese_id],
          ['curriculum' => [ 'name' => '助詞　助動詞','create_user_id' => 1 ],'subject_id' => $jh_japanese_id],
          ['curriculum' => [ 'name' => '式の計算　実数','create_user_id' => 1 ],'subject_id' => $h_math_I_id],
          ['curriculum' => [ 'name' => '１次不等式','create_user_id' => 1 ],'subject_id' => $h_math_I_id],
          ['curriculum' => [ 'name' => '集合　命題','create_user_id' => 1 ],'subject_id' => $h_math_I_id],
          ['curriculum' => [ 'name' => '２次関数','create_user_id' => 1 ],'subject_id' => $h_math_I_id],
          ['curriculum' => [ 'name' => '２次不等式','create_user_id' => 1 ],'subject_id' => $h_math_I_id],
          ['curriculum' => [ 'name' => '三角比','create_user_id' => 1 ],'subject_id' => $h_math_I_id],
          ['curriculum' => [ 'name' => 'データの分析','create_user_id' => 1 ],'subject_id' => $h_math_I_id],
          ['curriculum' => [ 'name' => '場合の数　確率','create_user_id' => 1 ],'subject_id' => $h_math_I_id],
          ['curriculum' => [ 'name' => '図形の性質','create_user_id' => 1 ],'subject_id' => $h_math_I_id],
          ['curriculum' => [ 'name' => '整数の性質','create_user_id' => 1 ],'subject_id' => $h_math_I_id],
          ['curriculum' => [ 'name' => 'ユークリッドの互除法','create_user_id' => 1 ],'subject_id' => $h_math_I_id],
          ['curriculum' => [ 'name' => '１次不定方程式','create_user_id' => 1 ],'subject_id' => $h_math_I_id],
          ['curriculum' => [ 'name' => 'ｎ 進法','create_user_id' => 1 ],'subject_id' => $h_math_I_id],
          ['curriculum' => [ 'name' => '式と証明','create_user_id' => 1 ],'subject_id' => $h_math_I_id],
          ['curriculum' => [ 'name' => '複素数','create_user_id' => 1 ],'subject_id' => $h_math_II_id],
          ['curriculum' => [ 'name' => '高次方程式','create_user_id' => 1 ],'subject_id' => $h_math_II_id],
          ['curriculum' => [ 'name' => '直線・円の方程式','create_user_id' => 1 ],'subject_id' => $h_math_II_id],
          ['curriculum' => [ 'name' => '軌跡　領域','create_user_id' => 1 ],'subject_id' => $h_math_II_id],
          ['curriculum' => [ 'name' => '三角関数','create_user_id' => 1 ],'subject_id' => $h_math_II_id],
          ['curriculum' => [ 'name' => '指数関数','create_user_id' => 1 ],'subject_id' => $h_math_II_id],
          ['curriculum' => [ 'name' => '対数関数','create_user_id' => 1 ],'subject_id' => $h_math_II_id],
          ['curriculum' => [ 'name' => '微分法','create_user_id' => 1 ],'subject_id' => $h_math_II_id],
          ['curriculum' => [ 'name' => '積分法','create_user_id' => 1 ],'subject_id' => $h_math_II_id],
          ['curriculum' => [ 'name' => 'ベクトル','create_user_id' => 1 ],'subject_id' => $h_math_II_id],
          ['curriculum' => [ 'name' => '数列','create_user_id' => 1 ],'subject_id' => $h_math_II_id],
          ['curriculum' => [ 'name' => '確率分布','create_user_id' => 1 ],'subject_id' => $h_math_II_id],
          ['curriculum' => [ 'name' => '複素数平面','create_user_id' => 1 ],'subject_id' => $h_math_III_id],
          ['curriculum' => [ 'name' => '２次曲線','create_user_id' => 1 ],'subject_id' => $h_math_III_id],
          ['curriculum' => [ 'name' => '媒介変数','create_user_id' => 1 ],'subject_id' => $h_math_III_id],
          ['curriculum' => [ 'name' => '極座標　極方程式','create_user_id' => 1 ],'subject_id' => $h_math_III_id],
          ['curriculum' => [ 'name' => '分数関数','create_user_id' => 1 ],'subject_id' => $h_math_III_id],
          ['curriculum' => [ 'name' => '無理関数','create_user_id' => 1 ],'subject_id' => $h_math_III_id],
          ['curriculum' => [ 'name' => '数列の極限','create_user_id' => 1 ],'subject_id' => $h_math_III_id],
          ['curriculum' => [ 'name' => '関数の極限','create_user_id' => 1 ],'subject_id' => $h_math_III_id],
          ['curriculum' => [ 'name' => '運動','create_user_id' => 1 ],'subject_id' => $h_physical_id],
          ['curriculum' => [ 'name' => '力学','create_user_id' => 1 ],'subject_id' => $h_physical_id],
          ['curriculum' => [ 'name' => '熱力学','create_user_id' => 1 ],'subject_id' => $h_physical_id],
          ['curriculum' => [ 'name' => '波動','create_user_id' => 1 ],'subject_id' => $h_physical_id],
          ['curriculum' => [ 'name' => '電磁気','create_user_id' => 1 ],'subject_id' => $h_physical_id],
          ['curriculum' => [ 'name' => 'コンデンサー','create_user_id' => 1 ],'subject_id' => $h_physical_id],
          ['curriculum' => [ 'name' => '交流回路','create_user_id' => 1 ],'subject_id' => $h_physical_id],
          ['curriculum' => [ 'name' => '量子力学','create_user_id' => 1 ],'subject_id' => $h_physical_id],
          ['curriculum' => [ 'name' => 'mol 計算','create_user_id' => 1 ],'subject_id' => $h_chemistry_id],
          ['curriculum' => [ 'name' => '溶解度','create_user_id' => 1 ],'subject_id' => $h_chemistry_id],
          ['curriculum' => [ 'name' => '電気分解','create_user_id' => 1 ],'subject_id' => $h_chemistry_id],
          ['curriculum' => [ 'name' => '反応速度','create_user_id' => 1 ],'subject_id' => $h_chemistry_id],
          ['curriculum' => [ 'name' => '化学平衡','create_user_id' => 1 ],'subject_id' => $h_chemistry_id],

        ];

        foreach($datam as $data){
          $item = Curriculum::create($data['curriculum']);
          $item->subjects()->attach($data['subject_id']);
        }
    }
}
