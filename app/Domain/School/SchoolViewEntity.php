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

    /**
     * 学校情報の基本情報
     *
     * @return array[]
     */
    public function fieldForIndex(): array
    {
        return [
            'name'         => [
                'label' => $this->localizeName('name'),
                'link'  => 'show',
            ],
            'address'      => [
                'label' => $this->localizeName('address'),
            ],
            'phone_number' => [
                'label' => $this->localizeName('phone_number'),
            ],
            'process'      => [
                'label' => $this->localizeName('process'),
            ],
            'buttons'      => [
                'label'  => $this->localizeName('control'),
                'button' => [
                      'school_textbooks' =>[
                        'style' =>'outline-primary',
                        'uri'  =>'/school_textbooks',
                        'label' => '使用テキスト',
                      ],
                     'edit',
                     'delete',
                ]
            ],
        ];
    }

    /**
     * 学校情報の詳細情報
     *
     * @return array[]
     */
    public function fieldForShow(): array
    {
        return [
            'name'             => [
                'label' => $this->localizeName('name'),
                'size'  => 6,
            ],
            'name_kana'        => [
                'label' => $this->localizeName('name_kana'),
                'size'  => 6,
            ],
            'post_number'      => [
                'label' => $this->localizeName('post_number'),
                'size'  => 6,
            ],
            'address'          => [
                'label' => $this->localizeName('address'),
                'size'  => 6,
            ],
            'phone_number'     => [
                'label' => $this->localizeName('phone_number'),
                'size'  => 6,
            ],
            'fax_number'       => [
                'label' => $this->localizeName('fax_number'),
                'size'  => 6,
            ],
            'process'          => [
                'label' => $this->localizeName('process'),
                'size'  => 6,
            ],
            'url'              => [
                'label' => $this->localizeName('url'),
                'size'  => 6,
            ],
            'department_names' => [
                'label' => $this->localizeName('department_names'),
                'size'  => 6,
            ],
            'access'           => [
                'label' => $this->localizeName('access'),
                'size'  => 6,
            ],
        ];
    }
}
