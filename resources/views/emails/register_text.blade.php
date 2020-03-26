@include('emails.common')
@yield('mail_title')
@if($send_to=="teacher")
{{__('messages.mail_dear_teacher', ['user_name' => $name_last])}}

{{__('messages.info_register_complete1', ['domain' => __('labels.teachers')])}}
{{__('messages.info_register_complete2')}}
@elseif($send_to=="manager")
{{__('messages.mail_dear_manager', ['user_name' => $name_last])}}

{{__('messages.info_register_complete1', ['domain' => __('labels.managers')])}}
{{__('messages.info_register_complete2')}}
@elseif($send_to=="parent")
{{$parent_name_last}} {{$parent_name_first}} 様
{{__('labels.system_name')}}への本登録が完了しました。

下記のログイン画面より、{{__('labels.system_name')}}を引き続きご利用ください。
@endif
…………………………………………………………………………………………
{{__('labels.login')}}
@if($send_to=="manager")
{{config('app.url')}}/managers/login
@else
{{config('app.url')}}/login
@endif
{{--  TODO : 実用化されるまでコメントアウト
{{__('labels.manual')}}
{{config('app.url')}}/manual

{{__('labels.faqs')}}
{{config('app.url')}}/faqs
--}}
…………………………………………………………………………………………

@yield('signature')
