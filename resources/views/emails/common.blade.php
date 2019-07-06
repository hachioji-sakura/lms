@section('mail_title')
{{__('messages.mail_header', ['url_name'=>__('labels.system_name'), 'url' =>config('app.url')])}}
@endsection

@section('signature')
{{__('messages.mail_footer')}}
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
{{__('labels.email')}}：hachiojisakura-support@hachioji-sakura.com
{{__('labels.phone_no')}}　：080-7726-2443 (042-649-3976)
http://hachiojisakura.com/
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Copyright © 八王子・日野の個別指導塾・個別塾【さくら個別アカデミー】八王子・豊田駅前 All Rights Reserved.
@endsection
