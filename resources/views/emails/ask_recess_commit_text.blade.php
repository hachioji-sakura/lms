@include('emails.common')

{{$user_name}} 様
ご連絡頂いた休会の件、承知致しました。

…………………………………………………………………………………………
{{__('labels.'.$ask->target_model)}}{{__('labels.name')}}: {{$target_model->name()}}
{{__('labels.recess')}}{{__('labels.day')}}: {{$ask["duration"]}}
{{__('labels.recess')}}{{__('labels.reason')}}:
{{$ask->body}}
…………………………………………………………………………………………

@yield('signature')
