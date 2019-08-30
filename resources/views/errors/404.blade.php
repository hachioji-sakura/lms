@extends('layouts.error')
@section('title', 'Page Not Found')
@section('return_button')
  <a href="/home"><button class="btn btn-primary">TOP画面へ</button></a>
@endsection
@section('error_title')
<b class="text-xl text-warning">404</b>エラー
@endsection
@section('error_description', 'Page Not Found')
@section('message')
  @if(empty($exception->getMessage()))
  ページがみつかりません
  @else
  {{$exception->getMessage()}}
  @endif
@endsection
