@include('emails.common')
@yield('mail_title')

{{__('messages.mail_dear', ['user_name' => $user_name])}}

{{__('messages.mail_password_reset1')}}
{{__('messages.info_url_limit')}}

…………………………………………………………………………………………
{{__('labels.password_setting')}}
{{config('app.url')}}/password/setting?key={{$access_key}}&locale={{$locale}}
…………………………………………………………………………………………

@yield('signature')
