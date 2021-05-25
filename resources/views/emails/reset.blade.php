<h3>
    <a href="{{ config('app.url') }}">{{ config('app.name') }}</a>
</h3>
<p>
    以下のURLより、パスワードを再設定してください<br>
</p>
<p>
    {{ $actionText }}: <a href="{{ $actionUrl }}">{{ $actionUrl }}</a>
</p>
<p>
    ※20日以内にアクセスしてください
</p>
