@extends('layouts.error')
@section('title', 'Access Forbidden')
@section('return_button')
  <a href="/home"><button class="btn btn-primary">TOP画面へ</button></a>
@endsection
@section('error_title')
<b class="text-xl text-danger">403</b>エラー
@endsection
@section('error_description', 'Access Forbidden')
@section('message')
  @if(empty($exception->getMessage()))
  このページへのアクセスはできません
  @else
  {{$exception->getMessage()}}
  @endif
@endsection
