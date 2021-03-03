@section('title')
  {{$domain_name}}成績一覧
@endsection
@extends('students.page')
@include($domain.'.menu')

@include('dashboard.widget.exams')

@section('sub_contents')
  <div class="col-12">
    @yield('exams')
  </div>
@endsection
