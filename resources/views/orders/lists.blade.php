@extends('dashboard.common')

@section('title')
{{$domain_name}}
@endsection

@section('title_header')
{{__('labels.agreements')}}
@endsection

@section('page_sidemenu')
@include('orders.sidemenu')
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
  @endslot
@endcomponent
@endsection

@include('dashboard.lists')
