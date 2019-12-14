@section('mail_title')
{{__('messages.mail_header', ['url_name'=>__('labels.system_name'), 'url' =>config('app.url')])}}
@endsection

@section('signature')
{!!nl2br(__('messages.mail_footer'))!!}
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
{{__('labels.email')}}：hachiojisakura-support@hachioji-sakura.com
{{__('labels.phone_no')}}　：080-7726-2443 (042-649-3976)
{{config('app.management_url')}}
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Copyright © 国立・八王子・日野の個別指導塾 さくら One All Rights Reserved.
@endsection
