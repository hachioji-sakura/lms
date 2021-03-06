@section('title')
  {{$domain_name}}一覧
@endsection
@extends('dashboard.common')
@section('contents')
    <div class="card-header">
      <h3 class="card-title">@yield('title')</h3>
    </div>
    <div class="card-body">
      <div class="card-body card-list" >
        <ul class="mailbox-attachments clearfix row">
          @foreach($items as $item)
          @if($item['chapter_count']>0)
          <li class="col-lg-3 col-md-4 col-12" accesskey="" target="">
            <input type="hidden" value="{{$loop->index}}" name="__index__" id="__index__">
            <input type="hidden" value="{{$item['id']}}" name="id">
            <div class="row">
              <div class="col-12 text-center">
                <a href="./{{$domain}}/{{$item['id']}}">
                  <img src="{{$item['icon']}}" class="mw-192px w-50" style="max-height:192px">
                </a>
              </div>
            </div>
            <div class="row">
              <div class="col-12 text-lg">
                <a href="./{{$domain}}/{{$item['id']}}">
                    {{str_limit($item['name'], 42, '...')}}
                </a>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                    {{str_limit($item['kana'], 42, '...')}}
              </div>
            </div>
          </li>
          @endif
          @endforeach
        </ul>
      </div>
    </div>
@endsection

@section('page_sidemenu')
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
  <li class="nav-item has-treeview menu-open mt-2">
    <a href="#" class="nav-link">
      <i class="nav-icon fa fa-filter"></i>
      <p>
        {{__('labels.filter')}}
        <i class="right fa fa-angle-left"></i>
      </p>
    </a>
    <ul class="nav nav-treeview">
      <li class="nav-item hr-1">
        @component('components.search_word', ['search_word' => $search_word])
        @endcomponent
      </li>
      <li class="nav-item">
        <a href="" class="nav-link">
          <i class="fa fa-users nav-icon"></i>すべて
        </a>
      </li>
    </ul>
  </li>
</ul>
@endsection

@section('page_footer')
@endsection
