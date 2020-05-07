
@extends('dashboard.common')

@section('title', $domain_name)

@section('page_sidemenu')
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
  <!--- スレッドビューまで封印
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
    -->
    <li class="nav-item has-treeview menu-open mt-2">
      <a href="#" class="nav-link">
        <i class="nav-icon fa fa-comment"></i>
        <p>
          {{__('labels.message')}}
          <i class="right fa fa-angle-left"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">
        @if($enable_create)
        <li class="nav-item">
          <a href="javascript:void(0);" page_form="dialog" page_url="/messages/create?id={{$id}}&domain={{$domain}}" page_title="メッセージ送信" class="nav-link">
            <i class="fa fa-plus nav-icon"></i>{{__('labels.new').__('labels.message')}}
          </a>
        </li>
        @endif
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
        @foreach($items as $item)
        <li class="item">
          <div class="row">
            <div class="col-6 text-truncate">
              <div class="row">
                <div class="col-12">
                  <a href="javascript:void(0)" page_url="/messages/{{$item->id}}/details" page_title="{{$item->title}}" page_form="dialog" title="{{$item->id}}">
                    {{$item->title}}
                  </a>
                </div>
              </div>
              <div class="row">
                <div class="col-12 text-truncate">
                  <small>
                    {{$item->body}}
                  </small>
                </div>
              </div>
            </div>
            <div class="col-1">
              @if(!empty($item->s3_url))
                 <i class="fas fa-paperclip"></i>
              @endif
            </div>
            <div class="col-5">
              <div class="row float-right">
                <div class="col-12">
                  <small>
                    <i class="fa fa-user mr-1"></i>
                    {{$item->create_user->details()->name()}}
                  </small>
                </div>
              </div>
              <div class="row mb-2 float-right">
                <div class="col-12">
                  <small class="text-muted">
                    <i class="fa fa-clock mr-1"></i>
                    {{$item->dateweek_format($item->created_at,'Y/m/d')}} {{date('H:m',strtotime($item->created_at))}}
                </small>
                </div>
              </div>
            </div>
            <div class="col-12">
              <div class="row">
                <div class="col-12">
                  @if($enable_create)
                  <a href="javascript:void(0);" page_form="dialog" page_url="/messages/{{$item->id}}/reply" page_title="{{__('labels.reply')}}" class="btn btn-primary btn-sm float-right">
                    <i class="fa fa-reply mr-1"></i>{{__('labels.reply')}}
                  </a>
                  @endif
                  <a href="javascript:void(0)" page_url="/messages/{{$item->id}}/details" page_title="{{$item->title}}" page_form="dialog" title="{{$item->id}}" class="btn btn-sm btn-secondary  float-right mr-2">
                    <i class="fa fa-file-alt mr-1"></i>{{__('labels.details')}}
                  </a>
                </div>
              </div>
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

@section('page_footer')
<dt>
  <a href="javascript:void(0);" page_form="dialog" page_url='/messages/create?id={{$id}}&domain={{$domain}}' page_title="{{__('labels.new')}}{{__('labels.message')}}" class="btn btn-app">
    <i class="fa fa-plus mr-1"></i>{{__('labels.new')}}
  </a>
</dt>
@endsection
