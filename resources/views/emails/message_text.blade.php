@include('emails.common')

{{$item->target_user->details()->name}} さん

{{$item->create_user->details()->name}} さん からメッセージが届きました。

{{$item->title}}

{{$item->body}}

※返信はシステムにログインして行ってください。
