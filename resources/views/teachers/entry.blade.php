@extends('layouts.loginbox')
@section('title', $domain_name.'仮登録')
@section('title_header', $domain_name.'仮登録')
@include($domain.'.entry_form')

@section('content')
<div id="{{$domain}}_entry">
@if(!empty($result))
  <h4 class="bg-success p-3 text-sm">
    @if($result==='success')
      仮登録完了メールを送信しました。<br>
      送信したメールにて、24時間以内にユーザー登録を進めてください。<br>
    @elseif($result==='already')
      仮登録中の情報が残っています。<br>
      再送信したメールにて、24時間以内にユーザー登録を進めてください。
    @elseif($result==='exist')
      このメールはユーザー登録が完了しています。
    @endif
  </h4>
@else
  <form method="POST"  action="/{{$domain}}/entry">
    @csrf
    @yield('entry_form')
    <div class="row">
      <div class="col-12 mb-1">
        <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="{{$domain}}_create">
          <i class="fa fa-envelope mr-1"></i>{{$domain_name}}仮登録を進める
        </button>
      </div>
    </div>
    <div class="row">
      <div class="col-12 mb-1">
        <a href="/login" role="button" class="btn btn-secondary btn-block float-left mr-1">
        ログイン画面へ戻る
      </a>
    </div>
  </div>
  </form>
</div>
@endif
@endsection
