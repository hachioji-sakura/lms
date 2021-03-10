<?php

namespace App\Domain\School;

use App\Domain\BaseEntity;
use LogicException;

/**
 * Class SchoolViewEntity
 *
 * @package App\Domain\School
 */
class SchoolViewEntity extends BaseEntity
{
    /**
     * @var string
     */
    protected $page_title = '';
    
    /**
     * ページタイトル
     *
     * @return string
     */
    public function pageTitle(): string
    {
        return __('labels.school_page_header_title');
    }
    
    /**
     * 過程一覧
     *
     * @return array
     */
    public function processList(): array
    {
        return [
            'fullDayGrade'            => '全日学年制',
            'fullDayCredit'           => '全日単位制',
            'partTimeGradeNightOnly'  => '定時制学年制夜間',
            'partTimeCredit'          => '定時単位制',
            'partTimeCreditNightOnly' => '定時単位夜間',
            'onlineSchool'            => '通信制'
        ];
    }
    
    /**
     * ローカライズ名を取得する
     *
     * @param string $key_name
     * @return string
     */
    public function localizeName(string $key_name): string
    {
        switch ($key_name) {
            case 'high_school_id':
                return __('labels.school_page_header_high_school_id');
            case 'post_number':
                return __('labels.school_page_header_post_number');
            case 'address':
                return __('labels.school_page_header_address');
            case 'phone_number':
                return __('labels.school_page_header_phone_number');
            case 'fax_number':
                return __('labels.school_page_header_fax_number');
            case 'access':
                return __('labels.school_page_header_access');
            case 'process':
                return __('labels.school_page_header_process');
            case 'school_type':
                return __('labels.school_page_header_school_type');
            case 'name':
                return __('labels.school_page_header_name');
            case 'name_kana':
                return __('labels.school_page_header_name_kana');
            case 'url':
                return __('labels.school_page_header_url');
            case 'department_names':
                return __('labels.school_page_header_department_names');
            case 'control':
                return __('labels.control');
            default:
                throw new LogicException('キーが誤っています');
        }
    }
}
