@extends('layouts.error')
@section('title', 'Session Timeout')
@section('error_title')
<b class="text-xl text-danger">419</b>エラー
@endsection
@section('error_description', 'セッションが切れました。')
@section('message', 'もう一度ログインしてください')
