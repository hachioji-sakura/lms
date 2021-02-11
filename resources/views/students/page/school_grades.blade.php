@section('title')
  {{$domain_name}}成績一覧
@endsection
@extends('students.page')
@include($domain.'.menu')

@include('dashboard.widget.school_grades')

@section('sub_contents')
  <div class="col-12">
    @yield('school_grades')
  </div>
@endsection
