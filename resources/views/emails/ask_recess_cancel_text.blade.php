@include('emails.common')

{{__('messages.info_recess')}}

…………………………………………………………………………………………
{{__('labels.'.$ask->target_model)}}{{__('labels.name')}}: {{$target_model->name()}}
{{__('labels.recess')}}{{__('labels.date')}}: {{$ask->start_date()}} ～ {{$ask->end_date()}}
{{__('labels.recess')}}{{__('labels.reason')}}:
{{$ask->body}}
…………………………………………………………………………………………

@yield('signature')
