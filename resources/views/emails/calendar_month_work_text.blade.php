@if($user->role==='teacher')
{{__('messages.mail_dear_teacher', ['user_name' => $user->name])}}
@elseif($user->role==='manager')
{{__('messages.mail_dear_manager', ['user_name' => $user->name])}}
@endif
{{__('messages.info_month_work_fix')}}
…………………………………………………………………………………………
{{date('Y/m', strtotime($target_month.'-01'))}}{{__('labels.work_record')}}
{{config('app.url')}}/{{$user->role}}s/{{$user->id}}/month_work/{{$target_month}}
…………………………………………………………………………………………
