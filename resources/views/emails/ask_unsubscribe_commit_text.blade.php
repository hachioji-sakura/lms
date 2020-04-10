@include('emails.common')

{{$user_name}} 様

ご連絡頂き、誠にありがとうございます。
以下の日付をもちまして退会されますこと、承知いたしました。

{{__('labels.unsubscribe')}}{{__('labels.day')}}: {{$ask["label_start_date"]}}


退会後も、さくらの卒業生としていつでも相談にのりますので、ご気軽にご連絡ください。

今まで授業を受けて頂き、大変感謝致します。

八王子さくらアカデミー代表
弓削 主哉

{{--
…………………………………………………………………………………………
{{__('labels.'.$ask->target_model)}}{{__('labels.name')}}: {{$target_model->name()}}
{{__('labels.unsubscribe')}}{{__('labels.day')}}: {{$ask["label_start_date"]}}
{{__('labels.unsubscribe')}}{{__('labels.reason')}}:
{{$ask->body}}
…………………………………………………………………………………………
@yield('signature')
--}}
