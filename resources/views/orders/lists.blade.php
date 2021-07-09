@extends('dashboard.common')

@section('title')
{{$domain_name}}
@endsection

@section('title_header')
{{__('labels.orders')}}
@endsection

@section('page_sidemenu')
  @include('orders.menu')
@endsection


@section('page_footer')
@endsection

@section('list_filter')
@component('components.list_filter_message', ['filter' => $filter, '_page' => $_page, '_line' => $_line, 'domain' => $domain, 'domain_name' => $domain_name, 'attributes'=>$attributes])
  @slot("search_form")
    <div class="col-12 mb-2">
      <label for="search_word" class="w-100">
        {{__('labels.search_keyword')}}
      </label>
      <input type="text" name="search_word" class="form-control" placeholder="" inputtype=""
      @isset($filter['search_keyword'])
      value = "{{$filter['search_keyword']}}"
      @endisset
      >
    </div>
    <div class="col-12 mb-2">
      <label for="search_status" class="w-100">
        {{__('labels.status')}}
      </label>
      <select name="search_status" class="form-control select2" width="100%" multiple="true">
        @foreach(config('attribute.order_status') as $key => $value)
          <option value="{{$key}}" {{request()->has('search_status') && request()->search_status == $key ? 'selected ': ''}}>{{$value}}</option>
        @endforeach
      </select>
    </div>
  @endslot
@endcomponent
@endsection

@include('dashboard.lists')
