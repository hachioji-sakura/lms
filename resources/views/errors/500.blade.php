@extends('layouts.error')
@section('title', 'System Error')
@section('error_title')
<b class="text-xl text-danger">500</b>エラー
@endsection
@section('error_description', 'SYSTEM ERROR')
@section('message')
  システムエラーが発生しました。
  @if(!empty($messgae))
    <br>
    {{$message}}
  @endif
@endsection
