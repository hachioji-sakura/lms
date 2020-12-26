■ご希望のコース
{{$item->get_tag_name('season_lesson_course')}}

■ご希望の教室
{{$item->get_tags_name('lesson_place')}}

■希望日時
@foreach($item->request_dates as $d)
{{$d->term}}
@endforeach

■希望科目（コマ数）
@foreach($item->charge_subject_attributes() as $attribute)
@if($item->get_tag_value($attribute->attribute_value.'_day_count')<1)
  @continue
@else
{{$attribute->attribute_name}}:{{$item->get_tag_value($attribute->attribute_value.'_day_count')}}
@endif
@endforeach

■通常授業を講習に振り替えますか？
@if(isset($item) && $item->has_tag('regular_schedule_exchange', 'true'))はい @elseいいえ@endif

■分割払い可能（3ヶ月）をご希望ですか？
@if(isset($item) && $item->has_tag('installment_payment', 'true'))はい @elseいいえ@endif

■学校の休み期間をおしらせください
@if(isset($item)){{$item->get_tag_name('school_vacation_start_date')}}～{{$item->get_tag_name('school_vacation_end_date')}}@endif

■特に重視してやって欲しいこと（その他）
@if(isset($item)){{$item->get_tags_name('entry_milestone_word')}}～{{$item->get_tag_name('school_vacation_end_date')}}@endif

■ご要望につきまして
@if(!empty($item->remark))
{{$item->remark}}
@else
-
@endif
