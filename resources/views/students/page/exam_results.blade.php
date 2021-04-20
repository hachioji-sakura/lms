@section('title')
  {{$domain_name}}試験一覧
@endsection
@extends('students.page')
@include($domain.'.menu')

@include('dashboard.widget.exam_results')

@section('sub_contents')
  <div class="col-12">
    @yield('exam_results')
  </div>
@endsection
