@extends('layouts.error')
@section('title', 'Bad Request')
@section('error_title')
<b class="text-xl text-danger">200</b>エラー
@endsection
@section('error_description', 'Bad Request')
@section('message')
  @if(empty($exception->getMessage()))
  リクエストが間違っています
  @else
  {{$exception->getMessage()}}
  @endif
@endsection
