@if($user->role==='teacher')
{{$user->name}}先生
@elseif($user->role==='manager')
{{$user->name}}さん
@endif
カレンダー修正依頼の連絡を受け付けました。

…………………………………………………………………………………………
対象年月
{{date('Y年m月', strtotime($target_month.'-01'))}}

勤務実績画面
{{config('app.url')}}/{{$user->role}}s/{{$user->id}}/month_work/{{$target_month}}
@if(isset($remark))
依頼内容
{{$remark}}
@endif
…………………………………………………………………………………………
