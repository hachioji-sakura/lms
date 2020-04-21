@include('emails.common')

{{$user->name()}} 様
以下の、休会依頼を登録いたしました。

…………………………………………………………………………………………
{{__('labels.'.$ask->target_model)}}{{__('labels.name')}}: {{$target_model->name()}}
{{__('labels.recess')}}{{__('labels.date')}}: {{$ask["duration"]}}
{{__('labels.recess')}}{{__('labels.reason')}}:
{{$ask->body}}
…………………………………………………………………………………………

■重要事項
1. 休会は２ヶ月間することが可能です。
但し、休会が明けた後、１ヶ月間は通塾することが条件となります。

2. また、休会の間、他の生徒様のご希望があった場合、現在通われている曜日及び時間に他の生徒様の予定が入ってしまうことがあります。

3. {{$ask["duration"]}}の授業は休会となります。

@yield('signature')
