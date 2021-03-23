@include('emails.common')
{{$user_name}} 様

ご依頼の通塾スケジュール等の変更に伴って、ご契約内容が変更になります。

ご契約内容をご確認の上ご了承頂けるようであれば、下記URLより
ホームページにアクセスしてご承認のほどよろしくお願いいたします。

…………………………………………………………………………………………
契約変更承認画面
{{config('app.url')}}/asks/{{$ask->id}}/agreement?key={{$target_model->student_parent->user->access_key}}
…………………………………………………………………………………………
ご契約内容
■基本契約内容
-----------------------------
@foreach($target_model->agreement_statements as $statement)
・({{config('attribute.lesson')[$statement->lesson_id]}})通塾回数/週 :{{$statement->lesson_week_count}} 回
@endforeach
・入会金 : &yen; {{number_format($target_model->entry_fee)}}
・月会費 : &yen; {{number_format($target_model->monthly_fee)}}

■通塾内容
@foreach($target_model->agreement_statements as $statement)
・概要：{{config('attribute.lesson')[$statement->lesson_id]}} / {{$statement->course_type_name}}
・授業時間：{{$statement->course_minutes_name}}
・校舎：{{$statement->user_calendar_member_settings->first()->setting->place_floor_name}}
・科目：@foreach($statement->user_calendar_member_settings->first()->setting->subject() as $subject) {{$subject}} @endforeach

・開始日：{{$statement->user_calendar_member_settings->first()->setting->details()['schedule_start_date']}}
・担当講師：{{$statement->teacher->details()->name()}}
・受講料： &yen; {{number_format($statement->tuition)}}

@endforeach

…………………………………………………………………………………………

どうぞよろしくお願い申し上げます。

SaKuRa One代表
弓削 主哉

@yield('signature')