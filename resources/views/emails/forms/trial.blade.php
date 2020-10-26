<?php
$item = $item->details();
 ?>
■ご希望ノレッスン
@foreach($item["tagdata"]['lesson'] as $label)
{{$label}}
@endforeach

■ご希望の授業時間
@foreach($item["tagdata"]['course_minutes'] as $label)
{{$label}}授業
@endforeach

■ご希望の教室
@foreach($item["tagdata"]['lesson_place'] as $label)
{{$label}}　
@endforeach

@if($item->is_trial_lesson_complete()==false || $item->status=='entry_contact')
■体験希望日時
第１希望：{{$item["date1"]}}
第２希望：{{$item["date2"]}}
第３希望：{{$item["date3"]}}
@else
@if($item->is_trial_lesson_complete()==false && !empty($item["start_hope_date"]))
■ご希望の授業開始日
{{$item["start_hope_date"]}}
@endif
@endif
@if(count($item["subject2"])>0)

■補習希望科目
@foreach($item["subject2"] as $label)
{{$label}}　
@endforeach
@endif
@if(count($item["subject1"])>0)

■受験希望科目
@foreach($item["subject1"] as $label)
{{$label}}　
@endforeach
@endif
@isset($item["tagdata"]['lesson'][2]) {{-- english_talk_lesson --}}
@isset($item["tagdata"]['english_teacher'])

■英会話希望講師
@foreach($item["tagdata"]['english_teacher'] as $label)
{{$label}}　
@endforeach
@endisset
@isset($item["tagdata"]['english_talk_lesson'])

■ご希望の英会話レッスン
@foreach($item["tagdata"]['english_talk_lesson'] as $label)
{{$label}}
@endforeach
@endisset
@isset($item["tagdata"]['english_talk_course_type'])

■授業形式(英会話）
@foreach($item["tagdata"]['english_talk_course_type'] as $label)
{{$label}}
@endforeach
@endisset
@endisset
@isset($item["tagdata"]['lesson'][3]) {{-- piano_lesson --}}
@isset($item["tagdata"]['piano_level'])

■ピアノのご経験
@foreach($item["tagdata"]['piano_level'] as $label)
{{$label}}　
@endforeach
@endisset
@endisset
@isset($item["tagdata"]['lesson'][4]){{-- kids_lesson --}}
@isset($item["tagdata"]['kids_lesson'])

■ご希望の習い事
@foreach($item["tagdata"]['kids_lesson'] as $label)
{{$label}}　
@endforeach
@endisset
@isset($item["tagdata"]['kids_lesson_course_type'])

■授業形式(習い事）
@foreach($item["tagdata"]['kids_lesson_course_type'] as $label)
{{$label}}
@endforeach
@endisset
@endisset

■通塾可能時間帯/曜日
@foreach(config('attribute.lesson_week') as $week_code => $week_name)
@foreach(config('attribute.lesson_time') as $index => $timezone)
@if(isset($item) && $item->has_tag('lesson_'.$week_code.'_time', $index)===true)
{{$week_name}} {{$timezone}}
@endif
@endforeach
@endforeach

■ご要望
@if(!empty($item["remark"]))
{{$item["remark"]}}
@else
-
@endif
