@include('emails.common')
{{$user_name}} 先生
季節講習の勤務可能日時登録を変更しました。
内容の変更・ご確認については、下記のＵＲＬをご利用ください。

{{config('app.url')}}/teachers/{{$domain_item_id}}/season_lesson?event_user_id={{$event_user_id}}&access_key={{$access_key}}

@yield('signature')
