@include('emails.common')
{{$user_name}} 先生
季節講習の勤務可能日時の登録が完了しました。
修正する場合は、以下のＵＲＬをご利用ください。

{{config('app.url')}}/{{$domain}}/{{$domain_item_id}}/season_lesson?event_user_id={{$event_user_id}}&access_key={{$access_key}}

@yield('signature')
