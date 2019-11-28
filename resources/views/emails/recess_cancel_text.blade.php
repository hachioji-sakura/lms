@include('emails.common')
{{$user->name()}} 様

@if($type=='unsubscribe')
退会依頼キャンセルについて、了承いたしました。
@else
休会依頼キャンセルについて、了承いたしました。
@endif

@if(count($user_calendar_members)>0)
以下の授業予定を再開いたしますので、
何卒宜しくお願い致します。

…………………………………………………………………………………………
@foreach($user_calendar_members as $member)
@component('emails.forms.calendar', ['item' => $member->calendar, 'send_to' => 'student', 'login_user' => $user]) @endcomponent
@endforeach
@else

@endif

@yield('signature')
