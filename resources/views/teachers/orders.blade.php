@section('title')
  {{$domain_name}} {{__('labels.orders')}}{{__('labels.list')}}
@endsection
@extends('dashboard.common')
{{--
@include($domain.'.menu')
--}}
@section('page_sidemenu')
  @include('orders.menu')
@endsection

@section('page_footer')
<dt>
  <a href="javascript:void(0);" page_title="{{__('labels.orders')}}{{__('labels.add')}}" page_form="dialog" page_url="/orders/create" class="btn btn-app">
    <i class="fa fa-plus nav-icon"></i>{{__('labels.orders')}}{{__('labels.add')}}
  </a>
</dt>
@endsection

@include('dashboard.lists')
