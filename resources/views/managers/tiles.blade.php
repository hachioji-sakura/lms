@extends('teachers.tiles')
@section('list_filter')
@component('components.list_filter', ['filter' => $filter, '_page' => $_page, '_line' => $_line, 'domain' => $domain, 'domain_name' => $domain_name, 'attributes'=>$attributes])
  @slot("search_form")
  <div class="col-12 col-md-4">
    <div class="form-group">
      <label for="is_desc" class="w-100">
        {{__('labels.sort_no')}}
      </label>
      <label class="mx-2">
      <input type="checkbox" value="1" name="is_desc" class="icheck flat-green"
      @if(isset($filter['sort']['is_desc']) && $filter['sort']['is_desc']==true)
        checked
      @endif
      >{{__('labels.created')}} {{__('labels.desc')}}
      </label>
    </div>
  </div>
  <div class="col-12 col-md-8">
      <label for="search_word" class="w-100">
        {{__('labels.search_keyword')}}
      </label>
      <input type="text" name="search_keyword" class="form-control" placeholder="" inputtype=""
      @if(isset($filter['search_keyword']))
      value = "{{$filter['search_keyword']}}"
      @endif
      >
  </div>
  @endslot
@endcomponent
@endsection

@section('contents')
@component('components.tiles', [
  'domain' => $domain, 'search_word'=>$search_word, 'items'=>$items, 'user'=>$user,
  'fields' => ['is_admin' => 'danger'],
])
@endcomponent
@endsection
