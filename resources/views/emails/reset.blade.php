<h3>
    <a href="{{ config('app.url') }}"></a>
</h3>
<p>
    以下のURLより、パスワードを再設定してください<br>
</p>
<p>
    {{ $actionText }}: <a href="{{ $actionUrl }}">{{ $actionUrl }}</a>
</p>
<p>
    ※24時間以内にアクセスしてください
</p>
