<?php
$tag = $item->get_tagdata();
?>
■ご希望ノレッスン
@foreach($tag['tagdata']['lesson'] as $label)
{{$label}}
@endforeach

■ご希望の授業時間
@foreach($tag['tagdata']['course_minutes'] as $label)
{{$label}}授業
@endforeach

■ご希望の教室
@foreach($tag['tagdata']['lesson_place'] as $label)
{{$label}}　
@endforeach

@if($item->is_request_lesson_complete()==false || $item->status=='entry_contact')
■体験希望日時
@foreach($item->request_dates as $d)
第{{$d->sort_no}}希望：{{$d->term}}
@endforeach
@else
@if($item->is_request_lesson_complete()==false && !empty($item->start_hope_date))
■ご希望の授業開始日
{{$item->start_hope_date}}
@endif
@endif
@if(count($tag["subject2"])>0)

■補習希望科目
@foreach($tag["subject2"] as $label)
{{$label}}　
@endforeach
@endif
@if(count($tag["subject1"])>0)

■受験希望科目
@foreach($tag["subject1"] as $label)
{{$label}}　
@endforeach
@endif
@isset($tag['tagdata']['lesson'][2]) {{-- english_talk_lesson --}}
@isset($tag['tagdata']['english_teacher'])

■英会話希望講師
@foreach($tag['tagdata']['english_teacher'] as $label)
{{$label}}　
@endforeach
@endisset
@isset($tag['tagdata']['english_talk_lesson'])

■ご希望の英会話レッスン
@foreach($tag['tagdata']['english_talk_lesson'] as $label)
{{$label}}
@endforeach
@endisset
@isset($tag['tagdata']['english_talk_course_type'])

■授業形式(英会話）
@foreach($tag['tagdata']['english_talk_course_type'] as $label)
{{$label}}
@endforeach
@endisset
@endisset
@isset($tag['tagdata']['lesson'][3]) {{-- piano_lesson --}}
@isset($tag['tagdata']['piano_level'])

■ピアノのご経験
@foreach($tag['tagdata']['piano_level'] as $label)
{{$label}}　
@endforeach
@endisset
@endisset
@isset($tag['tagdata']['lesson'][4]){{-- kids_lesson --}}
@isset($tag['tagdata']['kids_lesson'])

■ご希望の習い事
@foreach($tag['tagdata']['kids_lesson'] as $label)
{{$label}}　
@endforeach
@endisset
@isset($tag['tagdata']['kids_lesson_course_type'])

■授業形式(習い事）
@foreach($tag['tagdata']['kids_lesson_course_type'] as $label)
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
@if(!empty($item->remark))
{{$item->remark_full()}}
@else
-
@endif
