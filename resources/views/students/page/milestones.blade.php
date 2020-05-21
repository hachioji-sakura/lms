@section('title')
  {{$domain_name}}ダッシュボード
@endsection
@extends('students.page')
@include($domain.'.menu')

@include('dashboard.widget.milestones')

@section('sub_contents')
  <div class="col-12">
    @yield('milestones')   
  </div>
@endsection
