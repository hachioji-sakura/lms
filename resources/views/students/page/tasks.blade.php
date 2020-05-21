@section('title')
  {{$domain_name}}ダッシュボード
@endsection
@extends('students.page')
@include($domain.'.menu')

@include('dashboard.widget.tasks')

@section('sub_contents')
    @yield('tasks')
@endsection
