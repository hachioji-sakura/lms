@extends('dashboard.common')

@section('title_header',$domain_name)
@section('title',$domain_name)

@section('page_sidemenu')
 @include('tasks.menu')
@endsection

@section('contents')
  @include('tasks.contents')
@endsection
