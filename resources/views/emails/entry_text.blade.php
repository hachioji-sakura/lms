@include('emails.common')

@if($send_to=="teacher")
{!!nl2br(__('messages.mail_dear_teacher', ['user_name' => $user_name]))!!}

{!!nl2br(__('messages.info_register1', ['domain' => __('labels.teachers')])!!}

{!!nl2br(__('messages.info_register2'))!!}
{!!nl2br(__('messages.info_url_limit'))!!}
…………………………………………………………………………………………
{{__('labels.regiser')}}
{{config('app.url')}}/teachers/register?key={{$access_key}}
…………………………………………………………………………………………
@elseif($send_to=="manager")
{!!nl2br(__('messages.mail_dear_manager', ['user_name' => $user_name]))!!}
{!!nl2br(__('messages.info_register1', ['domain' => __('labels.managers')])!!}

{!!nl2br(__('messages.info_register2'))!!}
{!!nl2br(__('messages.info_url_limit'))!!}
…………………………………………………………………………………………
{{__('labels.regiser')}}
{{config('app.url')}}/managers/register?key={{$access_key}}
…………………………………………………………………………………………
@else
{{$user_name}}様

この度、ご入会いただき、誠にありがとうございます。

ご面倒おかけいたしますが、
当塾のシステムへの本登録をお願いいたします。

※URLの有効期限はこのメールの受信から24時間以内となっています。
…………………………………………………………………………………………
本登録画面
{{config('app.url')}}/register?key={{$access_key}}

当塾のシステムにつきまして
{{config('app.url')}}/faq

…………………………………………………………………………………………


@endif

@yield('signature')
