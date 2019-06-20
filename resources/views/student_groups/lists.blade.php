@section('title')
  {{$domain_name}}一覧
@endsection
@extends('dashboard.common')

@section('page_sidemenu')
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
  <li class="nav-item hr-1">
    @if(isset($teacher_id) && $teacher_id > 0)
    <a href="javascript:void(0);" page_title="{{$domain_name}}登録" page_form="dialog" page_url="/{{$domain}}/create?teacher_id={{$teacher_id}}" class="nav-link">
    @else
    <a href="javascript:void(0);" page_title="{{$domain_name}}登録" page_form="dialog" page_url="/{{$domain}}/create" class="nav-link">
    @endif
      <i class="fa fa-plus nav-icon"></i>{{$domain_name}}登録
    </a>
  </li>
  <li class="nav-item has-treeview menu-open mt-2">
    <a href="#" class="nav-link">
      <i class="nav-icon fa fa-filter"></i>
      <p>
        フィルタ
        <i class="right fa fa-angle-left"></i>
      </p>
    </a>
    <ul class="nav nav-treeview">
      <li class="nav-item">
         <a href="/{{$domain}}" class="nav-link">
           <i class="fa fa-calendar nav-icon"></i>すべて
         </a>
       </li>
    </ul>
  </li>
</ul>
@endsection


@section('contents')
<div class="card-header">
  <h3 class="card-title">@yield('title')</h3>
  <div class="card-tools pt-2">
    @component('components.list_pager', ['_page' => $_page, '_maxpage' => $_maxpage, '_list_start' => $_list_start, '_list_end'=>$_list_end, '_list_count'=>$_list_count]) @endcomponent
  </div>
</div>
<div class="card-body table-responsive p-0">
  @component('components.list_filter', ['_page' => $_page, '_line' => $_line, 'domain' => $domain, 'domain_name' => $domain_name, 'attributes'=>$attributes])
  @slot("search_form")
    <div class="col-4">
      <label for="charge_subject" class="w-100">
        タイプ
      </label>
      <div class="w-100">
        <select name="search_type[]" class="form-control select2" width=100% placeholder="検索タイプ" multiple="multiple" >
          @foreach($attributes['course_type'] as $index=>$name)
            @if($index==="single")
              @continue
            @endif
            <option value="{{$index}}"
            @if(isset($filter['search_type']) && in_array($index, $filter['search_type'])==true)
            selected
            @endif
            >{{$name}}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="col-4">
      <label for="charge_subject" class="w-100">
        キーワード
      </label>
      <div class="input-group mb-3">
        <input type="text" name="search_word" class="form-control" placeholder="キーワード検索" value="{{$search_word}}" style="width:140px;">
      </div>
    @endslot
  @endcomponent

  @component('components.list', ['items' => $items, 'fields' => $fields, 'domain' => $domain, 'domain_name' => $domain_name])
  @endcomponent
</div>
@endsection


@section('page_footer')
  <dt>
    <a class="btn btn-app"  href="javascript:void(0);" page_title="{{$domain_name}}登録" page_form="dialog" page_url="{{$domain}}/create">
      <i class="fa fa-plus"></i>{{$domain_name}}登録
    </a>
  </dt>
@endsection
