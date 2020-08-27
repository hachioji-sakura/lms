@extends('dashboard.common')

@section('title')
{{$domain_name}}
@endsection

@section('title_header')
{{__('labels.agreements')}}
@endsection

@section('page_sidemenu')
@endsection


@section('page_footer')
@endsection

@section('list_filter')
@endsection

@include('dashboard.lists')
