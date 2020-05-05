
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
        <div class="col-12">
          <a href="javascript:void(0);" page_form="dialog" page_url="/messages/create" page_title="メッセージ送信" role="button" class="btn btn-primary btn-sm" >
            <i class="fa fa-pen-square mr-2"></i>{{__('labels.new')}}
          </a>
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
    <div class="card-body table-responsive p-0">
      @if(count($items) > 0)
      <table class="table table-hover table-striped table-sm">
        <tbody>
          @foreach($fields as $field)
            @if($field['label'] == __('labels.message_type'))
            <th class="d-md-block d-none">
              {{$field['label']}}
            </th>
            @else
            <th>{{$field['label']}}</th>
            @endif
          @endforeach
          @foreach($items as $item)
          <tr>
            <td>
              <a href="/messages/{{$item->id}}/details" title="{{$item->id}}">
                <i class="fa fa-tag mr-1"></i>
                {{$item->title}}
                @if(!empty($item->s3_url))
                   <i class="fas fa-paperclip"></i>
                @endif
              </a>
            </td>
            <td class="d-md-block d-none">
              <span class="text-xs ms-2">
                <small class="badge badge-{{config('status_style')[$item->type]}}  ml-2 mr-1">
                  {{config('attribute.message_type')[$item->type]}}
                </small>
              </span>
            </td>
            <td>{{$item->create_user->details()->name()}}</td>
            <td>
                {{$item->created_at}}
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
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
