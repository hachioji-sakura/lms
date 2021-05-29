<?php

use Illuminate\Database\Seeder;

/**
 * Class SchoolSeeder
 */
class SchoolSeeder extends Seeder
{
    /**
     * @var array 高等学校の重複チェック用
     */
    protected $double_check_high_school = [];

    /**
     * @var array 特別支援学校の重複チェック用
     */
    protected $double_check_tokubetsushien = [];

    /**
     * @var string[]
     */
    protected static $departments_template = [
        '普通科',
        '普通科（コース制）',
        '農業科',
        '工業科',
        '工業科（デュアルシステム科）',
        '工業科（科学技術科）',
        '商業科',
        '商業科（ビジネスコミュニケーション科）',
        '情報科',
        '産業科',
        '家庭科',
        '福祉科',
        '芸術科',
        '体育科',
        '国際学科',
        '国際学科（国際バカロレアコース）',
        '総合学科',
        '併合科',
        '進学指導重点校',
        '進学指導特別推進校',
        '進学指導推進校',
        '進学指導研究校',
        '進学アシスト校',
        '学力向上研究校（校内寺子屋）',
        'ゆめナビプロジェクト研究校',
        'エンカレッジスクール',
        '東京グローバル１０',
        '英語教育推進校',
        '国際交流リーディング校',
        'スーパーサイエンスハイスクール',
        'Diverse Link Tokyo Edu',
        '理数アカデミー校',
        '理数リーディング校',
        '理数研究校',
        'チーム・メディカル',
        'アクティブ・ラーニング推進校',
        '知的探究イノベーター推進校',
        'ALCMコミュニティ参加校',
        '地域協働推進校（地域魅力化型）',
        '「新しい学び」の研究校',
        'ＢＹＯＤ研究指定校',
        'ＩＣＴパイロット校',
        '情報教育研究校',
        'チャレンジスクール（チャレンジ枠含む。）',
        'オリンピック・パラリンピック教育アワード校',
        'エンジョイスポーツプロジェクトモデル事業実施校',
        'スポーツ特別強化校',
        '文化部推進校',
        '文化部新設置推進校',
        '島外生徒受入実施校',
        'アドバンス校',
        '地域連携推進モデル校',
        '持続可能な社会づくりに向けた教育推進校',
        'ボランティア活動推進校',
        '人権尊重教育推進校',
        '安全教育推進校',
        '自転車安全運転指導推進校',
        'Society5.0に向けた学習方法研究校',
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // 何度でも実行できるように事前削除する
        DB::table('departments')->truncate();
        DB::table('schools')->truncate();
        DB::table('school_details')->truncate();
        DB::table('school_departments')->truncate();

        // 高等学校登録パターン1
//        $this->highSchoolSeeder('https://www.kyoiku.metro.tokyo.lg.jp/static/high_school/js/data.csv');

        // 学校の登録パターン2
        $attributes = [
            'youchien'       => [
                'id'          => '学校番号',
                'school_type' => 'kindergarten',
                'name'        => '園名（フリガナ）',
                'name_kana'   => '園名（フリガナ）',
                'csv_url'     => 'https://www.kyoiku.metro.tokyo.lg.jp/administration/statistics_and_research/list_of_public_school/files/report2020_csv/youchien-address.csv',
            ],
            'shougakkou'     => [
                'id'          => '学校番号',
                'school_type' => 'elementary_school',
                'name'        => '学校名',
                'name_kana'   => '学校名(フリガナ)',
                'csv_url'     => 'https://www.kyoiku.metro.tokyo.lg.jp/administration/statistics_and_research/list_of_public_school/files/report2020_csv/shougakkou-address.csv',
            ],
            'chuugakkou'     => [
                'id'          => '学校番号',
                'school_type' => 'junior_high_school',
                'name'        => '学校名',
                'name_kana'   => '学校名(フリガナ)',
                'csv_url'     => 'https://www.kyoiku.metro.tokyo.lg.jp/administration/statistics_and_research/list_of_public_school/files/report2020_csv/chuugakkou-address.csv',
            ],
            'gimu'           => [
                'id'          => '学校番号',
                'school_type' => 'compulsory_education_school',
                'name'        => '学校名',
                'name_kana'   => '学校名（フリガナ）',
                'csv_url'     => 'https://www.kyoiku.metro.tokyo.lg.jp/administration/statistics_and_research/list_of_public_school/files/report2020_csv/gimu-address.csv',
            ],
            'chuutou'        => [
                'id'          => '学校番号',
                'school_type' => 'secondary_school',
                'name'        => '学校名',
                'name_kana'   => '学校名(フリガナ)',
                'csv_url'     => 'https://www.kyoiku.metro.tokyo.lg.jp/administration/statistics_and_research/list_of_public_school/files/report2020_csv/chuutou-address.csv',
            ],
            'tokubetsushien' => [
                'id'          => '学校番号',
                'school_type' => 'special_school',
                'name'        => '学校名',
                'name_kana'   => '学校名(フリガナ)',
                'csv_url'     => 'https://www.kyoiku.metro.tokyo.lg.jp/administration/statistics_and_research/list_of_public_school/files/report2020_csv/tokubetsushien-adress.csv',
            ],
            'high_school'    => [
                'id'          => '学校番号',
                'school_type' => 'high_school',
                'name'        => '学校名',
                'name_kana'   => '学校名(フリガナ)',
                'csv_url'     => 'https://www.kyoiku.metro.tokyo.lg.jp/administration/statistics_and_research/list_of_public_school/files/report2020_csv/koutougakkou-ichiran-address.csv',
            ],
        ];

        // 各種学校情報を挿入
        foreach ($attributes as $attribute) {
            $this->seeding($attribute['csv_url'], $attribute);
        }
    }

    /**
     * Run the database seeds.
     *
     * @param  string  $url
     * @param  array  $attribute
     * @return void
     */
    public function seeding(string $url, array $attribute): void
    {
        $csv_rows = CSVReader::readWithKeyByUrl($url);

        $school_attributes = [];
        $high_school_attributes = [];
        foreach ($csv_rows as $csv_row) {
            // すでに登録済みの場合はスキップ（高等学校のみ複数箇所のCSVを読み込むため）
            if ($attribute['school_type'] === 'high_school' &&
                in_array($csv_row['電話番号'], $this->double_check_high_school, true)
            ) {
                continue;
            }
            // 高等学校の神田一橋だけが通信制が含まれており重複しているので除外しておく
            if (strpos($csv_row[$attribute['name']], '通信制') !== false) {
                continue;
            }

            // 空白対策としてID対象となるカラムがない場合はスキップ
            if (empty($csv_row[$attribute['id']])) {
                continue;
            }

            // 特別支援学校内の重複物は1つまでしか登録しない
            if ($attribute['school_type'] === 'special_school') {
                if (in_array($csv_row[$attribute['id']], $this->double_check_tokubetsushien, true)) {
                    continue;
                }

                $this->double_check_tokubetsushien[] = $csv_row[$attribute['id']];
            }

            // 学校
            $attributes = [];
            $attributes['id'] = $csv_row[$attribute['id']];
            $attributes['school_type'] = $attribute['school_type'];
            $attributes['name'] = $csv_row[$attribute['name']];
            $attributes['name_kana'] = $csv_row[$attribute['name_kana']];
            $attributes['url'] = '';
            $attributes['created_at'] = date('Y-m-d H:i:s', LARAVEL_START);
            $attributes['updated_at'] = date('Y-m-d H:i:s', LARAVEL_START);
            $school_attributes[] = $attributes;

            // 学校詳細
            $attributes = [];
            $attributes['id'] = $csv_row['学校番号'];
            $attributes['school_id'] = $csv_row['学校番号'];
            $attributes['post_number'] = $csv_row['郵便番号'];
            $attributes['address'] = $csv_row['住所'];
            $attributes['phone_number'] = $csv_row['電話番号'];
            $attributes['fax_number'] = '';
            $attributes['access'] = '';
            $attributes['full_day_grade'] = false;
            $attributes['full_day_credit'] = false;
            $attributes['part_time_grade_night_only'] = false;
            $attributes['part_time_credit'] = false;
            $attributes['part_time_credit_night_only'] = false;
            $attributes['online_school'] = false;
            $attributes['created_at'] = date('Y-m-d H:i:s', LARAVEL_START);
            $attributes['updated_at'] = date('Y-m-d H:i:s', LARAVEL_START);
            $high_school_attributes[] = $attributes;
        }

        // マルチインサート
        $school_attributes_chunk = collect($school_attributes)->chunk(256)->toArray();
        foreach ($school_attributes_chunk as $school_attributes_for_insert) {
            DB::table('schools')->insert($school_attributes_for_insert);
        }

        //        $high_school_attributes_chunk = collect($high_school_attributes)->chunk(256)->toArray();
//        foreach ($high_school_attributes_chunk as $high_school_attributes_for_insert) {
//            DB::table('school_details')->insert($high_school_attributes_for_insert);
//        }
    }

    /**
     * Run the database seeds.
     *
     * @param  string  $url
     * @return void
     */
    public function highSchoolSeeder(string $url): void
    {
        // 何度でも実行できるように事前削除する
        DB::table('departments')->truncate();
        DB::table('schools')->truncate();
        DB::table('school_details')->truncate();
        DB::table('school_departments')->truncate();

        $department_attributes = $this->initialize();
        $csv_rows = CSVReader::readWithKeyByUrl($url);

        $school_attributes = [];
        $high_school_attributes = [];
        $high_school_department_attributes = [];
        foreach ($csv_rows as $index => $csv_row) {
            // 学校
            $attributes = [];
            $attributes['id'] = $index + 1;
            $attributes['school_type'] = 'high_school';
            $attributes['name'] = $csv_row['﻿学校名'];
            $attributes['name_kana'] = $csv_row['読み方'];
            $attributes['url'] = $csv_row['学校ホームページ'];
            $attributes['created_at'] = date('Y-m-d H:i:s', LARAVEL_START);
            $attributes['updated_at'] = date('Y-m-d H:i:s', LARAVEL_START);
            $school_attributes[] = $attributes;

            // 高等学校
            $this->double_check_high_school[] = $csv_row['電話番号'];
            $attributes = [];
            $attributes['id'] = $index + 1;
            $attributes['school_id'] = $index + 1;
            $attributes['post_number'] = str_replace(PHP_EOL, '', $csv_row['郵便番号']);
            $attributes['address'] = $csv_row['住所'];
            $attributes['phone_number'] = $csv_row['電話番号'];
            $attributes['fax_number'] = $csv_row['ＦＡＸ番号'];
            $attributes['access'] = $csv_row['使用路線'];
            $attributes['full_day_grade'] = $csv_row['全日制（学年制）'] === '○';
            $attributes['full_day_credit'] = $csv_row['全日制（単位制）'] === '○';
            $attributes['part_time_grade_night_only'] = $csv_row['定時制（学年制・夜間）'] === '○';
            $attributes['part_time_credit'] = $csv_row['定時制（単位制・昼夜間）'] === '○';
            $attributes['part_time_credit_night_only'] = $csv_row['定時制（単位制・夜間）'] === '○';
            $attributes['online_school'] = $csv_row['通信制'] === '○';
            $attributes['created_at'] = date('Y-m-d H:i:s', LARAVEL_START);
            $attributes['updated_at'] = date('Y-m-d H:i:s', LARAVEL_START);
            $high_school_attributes[] = $attributes;

            // 高等学部
            foreach ($department_attributes as $department_name => $department_attribute) {
                if ($csv_row[$department_name] !== '○') {
                    continue;
                }
                $attributes = [];
                $attributes['school_type'] = 'high_school';
                $attributes['school_type_id'] = $index + 1;
                $attributes['department_id'] = $department_attributes[$department_name]['id'];
                $attributes['created_at'] = date('Y-m-d H:i:s', LARAVEL_START);
                $attributes['updated_at'] = date('Y-m-d H:i:s', LARAVEL_START);
                $high_school_department_attributes[] = $attributes;
            }
        }


        // マルチインサート
        $school_attributes_chunk = collect($school_attributes)->chunk(256)->toArray();
        foreach ($school_attributes_chunk as $school_attributes_for_insert) {
            DB::table('schools')->insert($school_attributes_for_insert);
        }
        $high_school_attributes_chunk = collect($high_school_attributes)->chunk(256)->toArray();
        foreach ($high_school_attributes_chunk as $high_school_attributes_for_insert) {
            DB::table('school_details')->insert($high_school_attributes_for_insert);
        }
        $high_school_department_attributes_chunk = collect($high_school_department_attributes)->chunk(256)->toArray();
        foreach ($high_school_department_attributes_chunk as $high_school_department_attributes_for_insert) {
            DB::table('school_departments')->insert($high_school_department_attributes_for_insert);
        }
    }

    /**
     * Seedを行うための事前準備
     *
     * @return array
     */
    private function initialize(): array
    {
        // 学部情報を先にインサート（あとで使いたいためキーを学部としておく）
        $department_attributes = [];
        $departments = static::$departments_template;
        foreach ($departments as $index => $department) {
            $attributes = [];
            $attributes['id'] = $index + 1;
            $attributes['department'] = $department;
            $attributes['created_at'] = date('Y-m-d H:i:s', LARAVEL_START);
            $attributes['updated_at'] = date('Y-m-d H:i:s', LARAVEL_START);
            $department_attributes[$department] = $attributes;
        }

        // マルチインサート
        $department_attributes_chunk = collect($department_attributes)->chunk(256)->toArray();
        foreach ($department_attributes_chunk as $department_attributes_for_insert) {
            DB::table('departments')->insert($department_attributes_for_insert);
        }

        return $department_attributes;
    }
}
