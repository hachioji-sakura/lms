@include('emails.common')

{{__('messages.info_unsubscribe_cancel')}}

…………………………………………………………………………………………
{{__('labels.'.$ask->target_model)}}{{__('labels.name')}}: {{$target_model->name()}}
{{__('labels.unsubscribe')}}{{__('labels.date')}}: {{$ask->start_date()}}
{{__('labels.unsubscribe')}}{{__('labels.reason')}}:
{{$ask->body}}
…………………………………………………………………………………………

@yield('signature')
