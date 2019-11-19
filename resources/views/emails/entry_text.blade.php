@include('emails.common')

@if($send_to=="teacher")
{{__('messages.mail_dear_teacher', ['user_name' => $user_name])}}

{{__('messages.info_register1', ['domain' => __('labels.teachers')])}}

{{__('messages.info_register2')}}
{{__('messages.info_url_limit')}}
…………………………………………………………………………………………
{{__('labels.register')}}
{{config('app.url')}}/teachers/register?key={{$access_key}}
…………………………………………………………………………………………
@elseif($send_to=="manager")
{{__('messages.mail_dear_manager', ['user_name' => $user_name])}}
{{__('messages.info_register1', ['domain' => __('labels.managers')])}}

{{__('messages.info_register2')}}
{{__('messages.info_url_limit')}}
…………………………………………………………………………………………
{{__('labels.register')}}
{{config('app.url')}}/managers/register?key={{$access_key}}
…………………………………………………………………………………………
@else
{{$user_name}} 様

いつも授業をお受けいただき、誠に感謝いたします。

この度、当塾のシステムへの登録を案内いいたします。
ご面倒をおかけいたしますが、登録いただけますと幸いです。

※URLの有効期限はこのメールの受信から24時間以内となっています。
…………………………………………………………………………………………
本登録画面
{{config('app.url')}}/register?key={{$access_key}}

当塾のシステムについて(FAQ)
{{config('app.url')}}/faqs

…………………………………………………………………………………………

@endif

@yield('signature')
