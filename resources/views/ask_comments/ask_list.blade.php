@section('title')
{{__('labels.ask_list')}}
@endsection
@extends('dashboard.common')

@section('contents')
<section class="content">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title" id="charge_students">
            <i class="fa fa-phone mr-1"></i>
            お問い合わせ一覧
          </h3>
          <div class="card-title text-sm">
            @component('components.list_pager', ['_page' => $_page, '_maxpage' => $_maxpage, '_list_start' => $_list_start, '_list_end'=>$_list_end, '_list_count'=>$_list_count])
              @slot("addon_button")
              <ul class="pagination pagination-sm m-0 float-left text-sm">
                <li class="page-item">
                  <a class="btn btn-info btn-sm" href="javascript:void(0);" page_form="dialog" page_url="/parents/{{$item->id}}/ask/create" page_title="お問い合わせ登録">
                    <i class="fa fa-plus"></i>
                    <span class="btn-label">
                      {{__('labels.add')}}
                    </span>
                  </a>
                </li>
              </ul>
              @endslot
            @endcomponent
          </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body table-responsive p-0">
          @if(count($asks) > 0)
          <ul class="mailbox-attachments clearfix row">
            @foreach($asks as $ask)
            <li class="col-12" accesskey="" target="">
              <div class="row">
                <div class="col-7 mt-1">
                  <a href="/parents/{{$item->id}}/ask/{{$ask->id}}" >
                    <i class="fa fa-phone mx-1"></i>{{$ask["type_name"]}}
                  </a>
                </div>
                <div class="col-5 mt-1 text-right">
                  <small title="{{$item["id"]}}" class="badge badge-{{config('status_style')[$ask->status]}} mr-1">
                    {{$ask->status_name()}}
                  </small>
                  <small title="{{$item["id"]}}" class="badge badge-info mr-1">
                    <i class="fa fa-comment-dots"></i> {{count($ask->comments)}}
                  </small>
                </div>
                <div class="col-12 mt-1 text-sm">
                @if($ask->target_model=='students')
                  生徒:{{$ask->get_target_model_data()->name()}}様<br>
                @endif
                @if($ask->type=='recess')
                  {{__('labels.duration')}}:{{$ask["duration"]}}
                @elseif($ask->type=='unsubscribe')
                  {{__('labels.unsubscribe')}}{{__('labels.day')}}:{{$ask["start_date"]}}
                @else
                @endif
                </div>
                <div class="col-12 text-xs mt-1 p-2 bd-t bd-gray" title="{{$ask->body}}">

                  {!!nl2br(str_limit($ask->body, 42,'...'))!!}
                </div>
                <div class="col-12 my-1 text-sm text-right text-muted">
                  @if($user->role=="teacher" || $user->role=="manager")
                  <i class="ml-2 fa fa-user"></i> {{$ask["create_user_name"]}}
                  @endif
                  <i class="ml-2 fa fa-clock"></i> {{$ask->created_at_label('Y年m月d日')}}
                </div>
                <div class="col-12 text-sm mt-1 text-right">
                  {{--
                  <a title="{{$ask["id"]}}" href="javascript:void(0);" page_title="依頼へのコメント" page_form="dialog" page_url="/asks/{{$ask->id}}/comments/create" role="button" class="btn btn-outline-info btn-sm">
                    <i class="fa fa-comment-dots mr-1"></i>
                    {{__('labels.comment_add')}}
                  </a>
                  --}}
                  <a title="{{$ask["id"]}}" href="javascript:void(0);" page_title="依頼内容編集" page_form="dialog" page_url="/parents/{{$item->id}}/ask/{{$ask->id}}/edit" role="button" class="btn btn-success btn-sm">
                    <i class="fa fa-edit mr-1"></i>
                    編集
                  </a>
                  <a title="{{$ask["id"]}}" href="javascript:void(0);" page_title="依頼内容削除" page_form="dialog" page_url="/asks/{{$ask->id}}?action=delete" role="button" class="btn btn-danger btn-sm">
                    <i class="fa fa-trash mr-1"></i>
                    削除
                  </a>
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
    </div>
  </div>
</section>

@component('components.list_filter', ['filter' => $filter, '_page' => $_page, '_line' => $_line, 'domain' => $domain, 'domain_name' => $domain_name, 'attributes'=>$attributes])
  @slot("search_form")
  <div class="col-12 col-md-4 mb-2">
    <label for="search_type" class="w-100">
      {{__('labels.ask_type')}}
    </label>
    <div class="w-100">
      <select name="search_type[]" class="form-control select2" width=100% multiple="multiple" >
        @foreach(config('attribute.ask_type') as $index => $name)
          <option value="{{$index}}"
          @if(isset($filter['search_type']) && in_array($index, $filter['search_type'])==true)
          selected
          @endif
          >{{$name}}</option>
        @endforeach
      </select>
    </div>
  </div>
  <div class="col-12 col-md-4 mb-2">
    <label for="search_status" class="w-100">
      {{__('labels.status')}}
    </label>
    <div class="w-100">
      <select name="search_status[]" class="form-control select2" width=100% multiple="multiple" >
        @foreach(config('attribute.ask_status') as $index => $name)
          <option value="{{$index}}"
          @if(isset($filter['search_status']) && in_array($index, $filter['search_status'])==true)
          selected
          @endif
          >{{$name}}</option>
        @endforeach
      </select>
    </div>
  </div>
  @endslot
@endcomponent
@endsection
