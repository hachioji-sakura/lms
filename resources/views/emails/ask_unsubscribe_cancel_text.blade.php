@include('emails.common')
{{$user->name()}} 様
{{__('messages.info_unsubscribe_cancel')}}

…………………………………………………………………………………………
{{__('labels.'.$ask->target_model)}}{{__('labels.name')}}: {{$target_model->name()}}
{{__('labels.unsubscribe')}}{{__('labels.date')}}: {{$ask["label_start_date"]}}
{{__('labels.unsubscribe')}}{{__('labels.reason')}}:
{{$ask->body}}
…………………………………………………………………………………………

@yield('signature')
