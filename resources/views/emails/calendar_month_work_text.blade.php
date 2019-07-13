@if($user->role==='teacher')
{!!nl2br(__('messages.mail_dear_teacher', ['user_name' => $user->name]))!!}
@elseif($user->role==='manager')
{!!nl2br(__('messages.mail_dear_manager', ['user_name' => $user->name]))!!}
@endif
{!!nl2br(__('messages.info_month_work_fix'))!!}
…………………………………………………………………………………………
{{date('Y/m', strtotime($target_month.'-01'))}}{{__('labels.work_record')}}
{{config('app.url')}}/{{$user->role}}s/{{$user->id}}/month_work/{{$target_month}}
…………………………………………………………………………………………
