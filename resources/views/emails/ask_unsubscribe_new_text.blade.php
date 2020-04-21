@include('emails.common')

{{$user->name()}} 様
以下の退会依頼を登録しました。

…………………………………………………………………………………………
{{__('labels.'.$ask->target_model)}}{{__('labels.name')}}: {{$target_model->name()}}
{{__('labels.unsubscribe')}}{{__('labels.date')}}: {{$ask["label_start_date"]}}
{{__('labels.unsubscribe')}}{{__('labels.reason')}}:
{{$ask->body}}
…………………………………………………………………………………………

@yield('signature')
