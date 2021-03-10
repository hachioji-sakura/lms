<?php

namespace App\Domain\School;

use App\Domain\BaseEntity;

/**
 * Class HighSchoolEntity
 *
 * @method highSchoolId
 * @method postNumber
 * @method address
 * @method phoneNumber
 * @method faxNumber
 * @method access
 * @method fullDayGrade
 * @method fullDayCredit
 * @method partTimeGradeNightOnly
 * @method partTimeCredit
 * @method partTimeCreditNightOnly
 * @method onlineSchool
 * @method schoolType
 * @method name
 * @method nameKana
 * @method url
 * @method departmentIds
 * @method departmentIdsCopy
 * @method departmentIdsChanged
 *
 * @package App\Domain\School
 */
class HighSchoolEntity extends BaseEntity
{
    /**
     * @var int
     */
    protected $high_school_id;
    
    /**
     * @var string
     */
    protected $post_number;
    
    /**
     * @var string
     */
    protected $address;
    
    /**
     * @var string
     */
    protected $phone_number;
    
    /**
     * @var string
     */
    protected $fax_number;
    
    /**
     * @var string
     */
    protected $access;
    
    /**
     * @var bool
     */
    protected $full_day_grade;
    
    /**
     * @var bool
     */
    protected $full_day_credit;
    
    /**
     * @var bool
     */
    protected $part_time_grade_night_only;
    
    /**
     * @var bool
     */
    protected $part_time_credit;
    
    /**
     * @var bool
     */
    protected $part_time_credit_night_only;
    
    /**
     * @var bool
     */
    protected $online_school;
    
    /**
     * @var string
     */
    protected $school_type;
    
    /**
     * @var string
     */
    protected $name;
    
    /**
     * @var string
     */
    protected $name_kana;
    
    /**
     * @var string
     */
    protected $url;
    
    /**
     * @var array
     */
    protected $department_names;
    
    /**
     * @var array
     */
    protected $department_ids;
    
    /**
     * @var array
     */
    protected $department_ids_copy;
    
    /**
     * @var bool
     */
    protected $department_ids_changed = false;
    
    /**
     * HighSchoolEntity constructor.
     *
     * @param array $high_school
     * @param array $schools
     * @param array $department_names
     * @param array $department_ids
     */
    public function __construct(
        array $high_school,
        array $schools,
        array $department_ids,
        array $department_names
    ) {
        // 高等学校関連
        $this->high_school_id = $high_school['id'];
        $this->post_number = $high_school['post_number'];
        $this->address = $high_school['address'];
        $this->phone_number = $high_school['phone_number'];
        $this->fax_number = $high_school['fax_number'];
        $this->access = $high_school['access'];
        $this->full_day_grade = (bool)$high_school['full_day_grade'];
        $this->full_day_credit = (bool)$high_school['full_day_credit'];
        $this->part_time_grade_night_only = (bool)$high_school['part_time_grade_night_only'];
        $this->part_time_credit = (bool)$high_school['part_time_credit'];
        $this->part_time_credit_night_only = (bool)$high_school['part_time_credit_night_only'];
        $this->online_school = (bool)$high_school['online_school'];
        
        // 学校関連
        $this->school_type = $schools['school_type'];
        $this->name = $schools['name'];
        $this->name_kana = $schools['name_kana'];
        $this->url = $schools['url'];
        
        // 学科関連
        $this->department_names = $department_names;
        $this->department_ids = $department_ids;
        $this->department_ids_copy = $department_ids;
    }
    
    /**
     * 高校の過程を取得する
     */
    public function process(): string
    {
        $result_text = '';
        
        if ($this->full_day_grade) {
            $result_text .= empty($result_text) ? '全日学年制' : ' / 全日学年制';
        }
        
        if ($this->full_day_credit) {
            $result_text .= empty($result_text) ? '全日単位制' : ' / 全日単位制';
        }
        
        if ($this->part_time_grade_night_only) {
            $result_text .= empty($result_text) ? '定時制学年制夜間' : ' / 定時制学年制夜間';
        }
        
        if ($this->part_time_credit) {
            $result_text .= empty($result_text) ? '定時単位制' : ' / 定時単位制';
        }
        
        if ($this->part_time_credit_night_only) {
            $result_text .= empty($result_text) ? '定時単位制夜間' : ' / 定時単位制夜間';
        }
        
        if ($this->online_school) {
            $result_text .= empty($result_text) ? '通信制' : ' / 通信制';
        }
        
        return $result_text;
    }
    
    /**
     * 学科名一覧
     *
     * @return string
     */
    public function departmentNames(): string
    {
        $text = '';
        foreach ($this->department_names as $index => $department_name) {
            if ($index === 0) {
                $text .= $department_name;
            } else {
                $text .= ', '.$department_name;
            }
        }
        
        return $text;
    }
    
    /**
     * 名前変更
     *
     * @param string $name
     */
    public function changeName(string $name): void
    {
        $this->name = $name;
    }
    
    /**
     * 名前変更（かなの変更）
     *
     * @param string $name
     */
    public function changeNameKana(string $name): void
    {
        $this->name_kana = $name;
    }
    
    /**
     * 郵便番号変更
     *
     * @param string $post_number
     */
    public function changePostNumber(string $post_number): void
    {
        $this->post_number = $post_number;
    }
    
    /**
     * 住所変更
     *
     * @param string $address
     */
    public function changeAddress(string $address): void
    {
        $this->address = $address;
    }
    
    /**
     * 電話番号変更
     *
     * @param string $phone_number
     */
    public function changePhoneNumber(string $phone_number): void
    {
        $this->phone_number = $phone_number;
    }
    
    /**
     * FAX番号変更
     *
     * @param string $fax_number
     */
    public function changeFaxNumber(string $fax_number): void
    {
        $this->fax_number = $fax_number;
    }
    
    /**
     * URL変更
     *
     * @param string $url
     */
    public function changeURL(string $url): void
    {
        $this->url = $url;
    }
    
    /**
     * 過程の変更
     *
     * @param array $process
     */
    public function changeProcess(array $process): void
    {
        $this->full_day_grade = in_array('fullDayGrade', $process, true);
        $this->full_day_credit = in_array('fullDayCredit', $process, true);
        $this->part_time_grade_night_only = in_array('partTimeGradeNightOnly', $process, true);
        $this->part_time_credit = in_array('partTimeCredit', $process, true);
        $this->part_time_credit_night_only = in_array('partTimeCreditNightOnly', $process, true);
        $this->online_school = in_array('onlineSchool', $process, true);
    }
    
    /**
     * 学科の変更
     *
     * @param array $department_ids
     */
    public function changeDepartment(array $department_ids): void
    {
        $this->department_ids = $department_ids;
        
        // 変更があったかどうかを明示的に管理する（処理の最適化のため）
        // updateの場合はLaravelでdirty処理がされるため自動的に最適化されるが、
        // 学科は削除→作成でデータを更新かける設計としているため、自前で最適化する
        if ($this->department_ids !== $this->department_ids_copy) {
            $this->department_ids_changed = true;
        }
    }
    
    /**
     * 使用路線の変更
     *
     * @param string $access
     */
    public function changeAccess(string $access): void
    {
        $this->access = $access;
    }
}
