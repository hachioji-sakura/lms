@section('title')
  {{$domain_name}}使用テキスト一覧
@endsection
@extends('students.page')
@include($domain.'.menu')

@include('dashboard.widget.student_textbooks')

@section('sub_contents')
  <div class="col-12">
    @yield('student_textbooks')
  </div>
@endsection
