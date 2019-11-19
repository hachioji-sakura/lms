@include('emails.common')

{{$user->name()}} 様
以下の、休会依頼をキャンセルいたしました。

…………………………………………………………………………………………
{{__('labels.'.$ask->target_model)}}{{__('labels.name')}}: {{$target_model->name()}}
{{__('labels.recess')}}{{__('labels.date')}}: {{$ask->start_date()}} ～ {{$ask->end_date()}}
{{__('labels.recess')}}{{__('labels.reason')}}:
{{$ask->body}}
…………………………………………………………………………………………

@yield('signature')
