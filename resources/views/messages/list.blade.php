
@extends('dashboard.common')

@section('title', $domain_name)

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
    @isset($item)
    <ul class="nav nav-treeview">
      @foreach(config('attribute.message_box') as $index => $name)
      <li class="nav-item">
         <a href="/{{$domain}}/{{$item->id}}/messages?search_list={{$index}}" class="nav-link @if(isset($search_list) && $index===$search_list) active @endif">
          <i class="fa fa-{{$name['icon']}} nav-icon"></i>{{$name['name']}}
         </a>
       </li>
       @endforeach
    </ul>
    @endif
    <li class="nav-item has-treeview menu-open mt-2">
      <a href="#" class="nav-link">
        <i class="nav-icon fa fa-comment"></i>
        <p>
          {{__('labels.message')}}
          <i class="right fa fa-angle-left"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item">
          <a href="javascript:void(0);" page_form="dialog" page_url="/messages/create" page_title="メッセージ送信" class="nav-link">
            <i class="fa fa-pen-square nav-icon"></i>{{__('labels.new').__('labels.message')}}
          </a>
        </li>
      </ul>
    </li>
  </li>
</ul>
@endsection

@section('contents')
  <div class="card">
    <div class="card-header">
      <div class="row">
        <div class="col-12">
          <h3 class="card-title">
            @isset($item)
              {{$item->details()->name()}} 様
            @else
              全件表示
            @endif
          </h3>
        </div>
        <div class="col-12">
          <h3 class="card-title">
            {{__('labels.message_list')}}
          </h3>
        </div>
      </div>
      <div class="card-tools">
        @component('components.search_word', ['search_word' => $search_word])
        @endcomponent
        <div class="pagenate">
          {{$items->appends(Request::query())->links('components.paginate')}}
        </div>
        <!-- 検索 -->
      </div>
    </div>
    <div class="card-body p-0">
      @if(count($items) > 0)
      <ul class="products-list product-list-in-card pl-2 pr-2">
        <li class="item  bg-light">
          <div class="row">
            @foreach($fields as $field)
              <div class="col-4">
                <label class="">
                  {{$field['label']}}
                </label>
              </div>
            @endforeach
          </div>
        </li>
        @foreach($items as $item)
        <li class="item">
          <div class="row">
            <div class="col-4 text-truncate">
              <a href="/messages/{{$item->id}}/details" title="{{$item->id}}">
                {{$item->title}}
              </a>
              @if(!empty($item->s3_url))
                 <i class="fas fa-paperclip"></i>
              @endif
            </div>
            <div class="col-4">
              {{$item->create_user->details()->name()}}
            </div>
            <div class="col-4">
              {{$item->dateweek_format($item->created_at,'Y/m/d H:m:s')}}
            </div>
          </div>
        </li>
        @endforeach
      </ul>
      @else
      <div class="alert">
        <h4><i class="icon fa fa-exclamation-triangle"></i>{{__('labels.no_data')}}</h4>
      </div>
      @endif
    </div>
  </div>
  @component('components.list_filter_message', ['filter' => $filter, '_page' => $_page, '_line' => $_line, 'domain' => $domain, 'domain_name' => $domain_name, 'attributes'=>$attributes])
    @slot('search_form')
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
