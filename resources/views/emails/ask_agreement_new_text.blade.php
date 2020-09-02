@include('emails.common')
{{$user_name}} 様
@component('asks.forms.agreement_policy', []) @endcomponent
1, 2, 3, 4, 5をご了承していただけますならば、
以下のURLからご入会登録をお願いいたします。

…………………………………………………………………………………………
入会登録画面
{{config('app.url')}}/asks/{{$ask->id}}/agreement?key={{$target_model->parent->user->access_key}}
…………………………………………………………………………………………
ご契約内容
■基本契約内容
-----------------------------
@for($i=1;$i<5;$i++)
@if($target_model->student->user->has_tag('lesson',$i)==false) @continue @endif
@if($target_model->student->user->get_enable_calendar_setting_count($i)==0) @continue @endif
・({{config('attribute.lesson')[$i]}})通塾回数/週　: 週{{$target_model->student->user->get_enable_calendar_setting_count($i)}}回
@endfor
・入会金 : @if($target_model->student->is_first_brother()==true) @component('trials.forms.entry_fee', ['user'=>$target_model->student->user]) @endcomponent @else 0円 @endif
・月会費 : @component('trials.forms.monthly_fee', ['user'=>$target_model->student->user, 'is_text' => true]) @endcomponent

■通塾内容
<?php
$tuition_form = [];
$is_exist = false;
?>
@foreach($target_model->student->user->get_enable_lesson_calendar_settings() as $lesson => $d0)
	@foreach($d0 as $schedule_method => $d1)
		@foreach($d1 as $lesson_week => $settings)
			@foreach($settings as $setting)
<?php
$setting = $setting->details();
?>
-----------------------------
・概要：{{$setting->lesson()}} / {{$setting->course()}}
・{{$setting->schedule_method()}}{{$setting["week_setting"]}}/{{$setting["timezone"]}}
・授業時間：{{$setting["course_minutes_name"]}}
・校舎：{{$setting["place_floor_name"]}}
・科目：@foreach($setting->subject() as $subject) {{$subject}} @endforeach

・開始日：{{$setting['schedule_start_date']}}
・担当講師：{{$setting["teacher_name"]}}
<?php
$is_exist = true;
$setting_key = $setting->get_tag_value('lesson').'_';
$setting_key .= $setting->get_tag_value('course_type').'_';
$setting_key .= $setting->course_minutes.'_';
$setting_key .= $setting->user_id.'_';
if($setting->get_tag_value('lesson')==2 && $setting->has_tag('english_talk_lesson', 'chinese')==true){
$setting_key .= $setting->get_tag_value('subject');
}
else if($setting->get_tag_value('lesson')==4){
$setting_key .= $setting->get_tag_value('kids_lesson');
}
?>
・受講料：@if(!empty($setting->get_tuition($target_model->student->user_id))) &yen;{{number_format($setting->get_tuition($target_model->student->user_id))}} / 時間 @else 受講料設定がありません
@endif

			@endforeach
		@endforeach
	@endforeach
@endforeach

…………………………………………………………………………………………

どうぞよろしくお願い申し上げます。

SaKuRa One代表
弓削 主哉

@yield('signature')
