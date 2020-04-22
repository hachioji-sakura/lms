@include('emails.common')

{{$item->target_user->details()->name}} さん

{{$item->create_user->details()->name}} さん
からメッセージが届きました。

概要:{{$item->title}}

内容:
{{$item->body}}


【{{__('labels.important')}}】
{{__('messages.mail_footer')}}
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
{{__('labels.email')}}：hachiojisakura-support@hachioji-sakura.com
{{__('labels.phone_no')}}　：080-7726-2443 (042-649-3976)
{{config('app.management_url')}}
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Copyright © 国立・八王子・日野の個別指導塾 さくら One All Rights Reserved.
