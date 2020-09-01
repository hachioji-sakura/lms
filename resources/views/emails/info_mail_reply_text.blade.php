@include('emails.common')

本アドレスは送信専用のため、ご送信いただきましたEメールはお相手に到達しておりません。
下記URLよりシステムにログインしてご返信ください。
https://sakuraone.jp/

Since this address is for sending only, the email you sent has not reached the recipient.
Please log in to the system from the URL below and reply.

https://sakuraone.jp/

-----------------------------
ご返信内容 (Reply Mail)
----------------------------
@if(isset($subject) && !empty($subject))
{{__('labels.message_title')}}：
{{$subject}}
@endif
@if(isset($body) && !empty($body))
{{__('labels.contents')}}：
{{$body}}
@endif
----------------------------

@yield('signature')
