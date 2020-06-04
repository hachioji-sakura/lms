@include('emails.common')
{{$user_name}} 様

{{__('messages.message_trial_candidate_date1')}}

@if(!empty($add_message))
{{$add_message}}
@endif

{{__('messages.message_trial_candidate_date2')}}
{{config('app.url')}}/trials/{{$item->id}}/candidate_date?key={{$access_key}}

…………………………………………………………………………………………
@component('emails.forms.trial', ['item' => $item,  'login_user' => $login_user]) @endcomponent
…………………………………………………………………………………………

@yield('signature')
