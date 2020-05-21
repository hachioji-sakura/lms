@section('title')
  {{$domain_name}}ダッシュボード
@endsection
@extends('students.page')
@include($domain.'.menu')

@include('dashboard.widget.star_comments')

@section('sub_contents')
  @yield('star_comments')
  @yield('comments')
@endsection
