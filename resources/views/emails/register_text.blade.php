@include('emails.common')
@yield('mail_title')
@if($send_to=="teacher")
{!!nl2br(__('messages.mail_dear_teacher', ['user_name' => $name_last]))!!}

{!!nl2br(__('messages.info_register_complete1', ['domain' => __('labels.teachers')]))!!}
{!!nl2br(__('messages.info_register_complete2'))!!}
@elseif($send_to=="manager")
{!!nl2br(__('messages.mail_dear_manager', ['user_name' => $name_last]))!!}

{!!nl2br(__('messages.info_register_complete1', ['domain' => __('labels.managers')]))!!}
{!!nl2br(__('messages.info_register_complete2'))!!}
@elseif($send_to=="parent")
{{$parent_name_last}} {{$parent_name_first}}様
この度、入会お申込み誠にありがとうございます。

下記のログイン画面より、学習管理システムをご利用ください。
@endif
…………………………………………………………………………………………
{{__('labels.login')}}
@if($send_to=="manager")
{{config('app.url')}}/managers/login
@else
{{config('app.url')}}/login
@endif

{{__('labels.manual')}}
{{config('app.url')}}/manual

{{__('labels.faq')}}
{{config('app.url')}}/faq

…………………………………………………………………………………………

@yield('signature')
