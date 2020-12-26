■ご希望ノレッスン
{{$item->get_tags_name('lesson')}}

■ご希望の授業時間
{{$item->get_tag_name('course_minutes')}}授業

■ご希望の教室
{{$item->get_tags_name('lesson_place')}}

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
@if(count($item->get_subject())>0)

■補習希望科目
@foreach($item->get_subject() as $label)
{{$label}}　
@endforeach
@endif
@if(count($item->get_subject(true))>0)

■受験希望科目
@foreach($item->get_subject(true) as $label)
{{$label}}　
@endforeach
@endif
@if($item->has_tag('lesson', 2))

■英会話希望講師
{{$item->get_tag_name('english_teacher')}}　

■ご希望の英会話レッスン
{{$item->get_tag_name('english_talk_lesson')}}　

■授業形式(英会話）
{{$item->get_tag_name('english_talk_course_type')}}　
@endif

@if($item->has_tag('lesson', 3))

■ピアノのご経験
{{$item->get_tag_name('piano_level')}}　
@endif
@if($item->has_tag('lesson', 4))

■ご希望の習い事
{{$item->get_tags_name('kids_lesson')}}　
　
■授業形式(習い事）
{{$item->get_tag_name('kids_lesson_course_type')}}　
@endif

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
