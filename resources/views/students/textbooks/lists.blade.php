@section('title')
  {{$domain_name}} {{__('labels.list')}}
@endsection

@section('list_pager')
  <div class="card-title text-sm">
    {{$items->appends(Request::query())->links('components.paginate')}}
  </div>
@endsection

@include('dashboard.lists')
@extends('dashboard.common')
