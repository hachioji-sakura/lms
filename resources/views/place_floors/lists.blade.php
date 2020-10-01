@section('title')
{{__('labels.place_floors')}}
@endsection
@section('title_header')
{{__('labels.place_floors')}}
@endsection

@section('list_filter')
  @component('components.list_filter', ['filter' => $filter, '_page' => $_page, '_line' => $_line, 'domain' => $domain, 'domain_name' => $domain_name, 'attributes'=>$attributes])
    @slot("search_form")
    <div class="col-12 col-md-4">
      <div class="form-group">
        <label for="is_desc_1" class="w-100">
          {{__('labels.sort_no')}}
        </label>
        <label class="mx-2">
        <input type="checkbox" value="1" name="is_desc" class="icheck flat-green" id="is_desc_1"
        @if(isset($filter['sort']['is_desc']) && $filter['sort']['is_desc']==true)
          checked
        @endif
        >{{__('labels.sort_no')}} {{__('labels.desc')}}
        </label>
      </div>
    </div>
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

@section('page_sidemenu')
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
  <li class="nav-item">
    <a href="/places" class="nav-link">
      <i class="fa fa-arrow-left nav-icon"></i>{{__('labels.places')}}{{__('labels.back_button')}}
    </a>
  </li>
  <li class="nav-item hr-1">
    <a href="javascript:void(0);" page_title="{{$domain_name}}{{__('labels.add')}}" page_form="dialog" page_url="/{{$domain}}/create?place_id={{$filter['user_filter']['place_id']}}" class="nav-link">
      <i class="fa fa-plus nav-icon"></i>{{$domain_name}}{{__('labels.add')}}
    </a>
  </li>
  <li class="nav-item has-treeview menu-open mt-2">
    <a href="#" class="nav-link">
      <i class="nav-icon fa fa-filter"></i>
      <p>
        {{__('labels.filter')}}
        <i class="right fa fa-angle-left"></i>
      </p>
    </a>
    <ul class="nav nav-treeview">
      @foreach($attributes['places'] as $place)
      <li class="nav-item">
         <a href="/{{$domain}}?place_id={{$place->id}}" class="nav-link
         @if(isset($filter['user_filter']['place_id']) && $place->id==$filter['user_filter']['place_id'])
          active
         @endif
         ">
           <i class="fa fa-map-marker-alt nav-icon"></i>{{$place->name}}
         </a>
       </li>
       @endforeach
    </ul>
  </li>
</ul>
@endsection

@section('page_footer')
  <dt>
    {{--
    <a class="btn btn-app"  href="javascript:void(0);" page_title="{{$domain_name}}登録" page_form="dialog" page_url="{{$domain}}/create">
      <i class="fa fa-plus"></i>{{$domain_name}}登録
    </a>
    --}}
  </dt>
@endsection

@extends('dashboard.common')
@include('dashboard.lists')
