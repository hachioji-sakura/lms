@extends('layouts.error')
@section('return_button')
  <a href="/home"><button class="btn btn-primary">TOP画面へ</button></a>
@endsection
@section('title', 'Bad Request')
@section('error_title')
<b class="text-xl text-danger">400</b>エラー
@endsection
@section('error_description', 'Bad Request')
@section('message')
  @if(empty($exception->getMessage()))
  リクエストが間違っています
  @else
  {{$exception->getMessage()}}
  @endif
@endsection
