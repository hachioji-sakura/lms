@extends('layouts.error')
@section('return_button')
  <a href="/login"><button class="btn btn-primary">ログイン画面へ</button></a>
@endsection
@section('title', 'Session Timeout')
@section('error_title')
<b class="text-xl text-danger">419</b>エラー
@endsection
@section('error_description', 'セッションが切れました。')
@section('message', 'もう一度ログインしてください')
