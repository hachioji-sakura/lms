@include('emails.common')
@yield('mail_title')

{!!nl2br(__('messages.mail_dear', ['user_name' => $user_name]))!!}

{!!nl2br(__('messages.mail_password_reset1'))!!}
{!!nl2br(__('messages.info_url_limit'))!!}

…………………………………………………………………………………………
{{__('labels.password_setting')}}
{{config('app.url')}}/password/setting?key={{$access_key}}&locale={{$locale}}
…………………………………………………………………………………………

@yield('signature')
