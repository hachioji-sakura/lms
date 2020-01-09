@section('title')
{{__('labels.comments_list')}}
@endsection
@extends('dashboard.common')
@section('contents')
<div class="col-12">
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">
        <i class="fa fa-bullhorn"></i>
        {{__('labels.announcements')}}
      </h3>
      <div class="card-tools">
        {{-- TODO
        @if($user->role == "manager")
        <a class="btn btn-tool" href="javascript:void(0);" page_form="dialog" page_url="/comments/create?origin={{$domain}}&item_id={{$item->id}}" page_title="{{__('labels.comment_add')}}">
            <i class="fa fa-pen nav-icon"></i>
        </a>
        @endif
        --}}
        <a class="btn btn-tool" data-toggle="modal" data-target="#filter_form" id="filter_button">
            <i class="fa fa-filter nav-icon"></i>
        </a>
        <button type="button" class="btn btn-tool" data-widget="collapse">
          <i class="fa fa-minus"></i>
        </button>
      </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body p-0">
      <ul class="products-list product-list-in-card pl-2 pr-2">
        <?php $is_exist=false; ?>
        @foreach($items as $comment)
        <?php
          $comment = $comment->details();
          $is_exist = true;
        ?>

        <li class="item">
          <div class="">
            <a href="javascript:void(0);" page_url="/comments/{{$comment->id}}" page_title="" class="product-title">
              {{$comment["type_name"]}}
              <span class="text-sm text-muted float-right mb-1">
                <i class="ml-2 fa fa-user-check"></i> {{__('labels.target_user')}}：{{$comment["target_user_name"]}}
                <i class="ml-2 fa fa-pen"></i> {{__('labels.create_user')}}：{{$comment["create_user_name"]}}
                <i class="ml-2 fa fa-clock"></i>{{$comment["created_date"]}}
              </span>
            </a>
            <span class="product-description">
                {!!nl2br($comment->body)!!}

                @if(!empty($comment->s3_url))
                <br>
                <span class="mr-1">
                  <a href="{{$comment->s3_url}}" target="_blank">
                    <i class="fa fa-link mr-1"></i>
                    {{$comment->s3_alias}}
                  </a>
                </span>
                @endif

            </span>

            <br>
            <span class="float-right mr-1">
              @if($user->user_id === $comment->create_user_id)
              <a href="javascript:void(0);" page_title="コメント編集" page_form="dialog" page_url="/comments/{{$comment->id}}/edit" role="button" class="btn btn-default btn-sm float-left mr-1">
                <i class="fa fa-edit"></i>
              </a>
              <a href="javascript:void(0);" page_title="コメント削除" page_form="dialog" page_url="/comments/{{$comment->id}}?action=delete}}" role="button" class="btn btn-default btn-sm float-left mr-1">
                <i class="fa fa-trash"></i>
              </a>
              @endif
              <a id="importance_button_{{$comment->id}}" href="javascript:void(0);" onClick="importance_comment({{$comment->id}});" role="button" class="btn
                @if($comment->importance > 5  )
                  btn-danger
                @elseif($comment->importance > 1)
                  btn-warning
                @else
                  btn-secondary
                @endif
                btn-sm float-left mr-1">
                <i class="fa fa-thumbtack"></i>
              </a>
              <a id="check_button_{{$comment->id}}" href="javascript:void(0);" onClick="comment_check({{$comment->id}});" role="button" class="btn
                @if($comment->is_check($user->user_id)==false)
                btn-outline-secondary
                @else
                btn-outline-primary
                @endif
                btn-sm float-left mr-1">
                <i class="fa fa-check-circle"></i>
              </a>
              <script>
              function importance_comment(id){
                  service.postAjax('/comments/'+id+'/importanced',null,
                  function(result, st, xhr) {
                    if(result['status']===200){
                      console.log(result['data']);
                      $("#importance_button_"+id).removeClass("btn-secondary");
                      $("#importance_button_"+id).removeClass("btn-danger");
                      $("#importance_button_"+id).removeClass("btn-warning");
                      if(result['data']['importance']>5){
                        $("#importance_button_"+id).addClass("btn-danger");
                      }
                      else if(result['data']['importance']>1){
                        $("#importance_button_"+id).addClass("btn-warning");
                      }
                      else{
                        $("#importance_button_"+id).addClass("btn-secondary");
                      }
                    }
                  },
                  function(xhr, st, err) {
                      messageCode = "error";
                      messageParam= "\n"+err.message+"\n"+xhr.responseText;
                      alert("システムエラーが発生しました\n"+messageParam);
                  }, "PUT");
              }
              function comment_check(id){
                  service.postAjax('/comments/'+id+'/checked',null,
                  function(result, st, xhr) {
                    if(result['status']===200){
                      console.log(result['data']);
                      if(result['data']['is_checked'] == false){
                        $("#check_button_"+id).removeClass("btn-outline-primary");
                        $("#check_button_"+id).addClass("btn-outline-secondary");
                      }
                      else {
                        $("#check_button_"+id).removeClass("btn-outline-secondary");
                        $("#check_button_"+id).addClass("btn-outline-primary");
                      }
                    }
                  },
                  function(xhr, st, err) {
                      messageCode = "error";
                      messageParam= "\n"+err.message+"\n"+xhr.responseText;
                      alert("システムエラーが発生しました\n"+messageParam);
                  }, "PUT");
              }
              </script>
            </span>

          </div>
        </li>
        @endforeach
        @if($is_exist == false)
        <li class="item">
          <div class="alert">
            <h4><i class="icon fa fa-exclamation-triangle"></i>{{__('labels.no_data')}}</h4>
          </div>
        </li>
        @endif
      </ul>
    </div>
    <!-- /.card-body -->
    <div class="card-footer text-center">
      <a href="/{{$user["domain"]}}/{{$user->id}}/comments" class="uppercase">
        <i class="fa fa-arrow-right mr-1"></i>
        {{__('labels.all_announcements')}}
      </a>
    </div>
    <!-- /.card-footer -->
  </div>
</div>
@component('components.list_filter', ['filter' => $filter, '_page' => $_page, '_line' => $_line, 'domain' => $domain, 'domain_name' => $domain_name, 'attributes'=>$attributes])
  @slot("search_form")
  <div class="col-6 col-md-4">
    <div class="form-group">
      <label for="search_from_date" class="w-100">
        {{__('labels.date')}}(FROM)
      </label>
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text"><i class="fa fa-calendar"></i></span>
        </div>
        <input type="text" id="search_from_date" name="search_from_date" class="form-control float-left" uitype="datepicker" placeholder="2000/01/01"
        @if(isset($filter['search_from_date']))
          value="{{$filter['search_from_date']}}"
        @endif
        >
      </div>
    </div>
  </div>
  <div class="col-6 col-md-4">
    <div class="form-group">
      <label for="search_to_date" class="w-100">
        {{__('labels.date')}}(TO)
      </label>
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text"><i class="fa fa-calendar"></i></span>
        </div>
        <input type="text" id="search_to_date" name="search_to_date" class="form-control float-left" uitype="datepicker" placeholder="2000/01/01"
        @if(isset($filter['search_to_date']))
          value="{{$filter['search_to_date']}}"
        @endif
        >
      </div>
    </div>
  </div>
  <div class="col-12 mb-2">
    <label for="search_status" class="w-100">
      {{__('labels.comments')}}
      {{__('labels.type')}}
    </label>
    <div class="w-100">
      <select name="search_comment_type[]" class="form-control select2" width=100% placeholder="検索タイプ" multiple="multiple" >
          @foreach(config('attribute.comment_type') as $index => $name)
            <option value="{{$index}}"
            @if(isset($filter['comment_filter']['search_comment_type']) && in_array($index, $filter['comment_filter']['search_comment_type'])==true)
            selected
            @endif
            >{{$name}}</option>
          @endforeach
      </select>
    </div>
  </div>
  <div class="col-12 mb-2">
    <div class="form-group">
      <label for="search_keyword" class="w-100">
        {{__('labels.search_keyword')}}
      </label>
      <input type="text" name="search_keyword" class="form-control" placeholder="{{__('labels.search_keyword')}}"
      @if(isset($filter['search_keyword']))
        value="{{$filter['search_keyword']}}"
      @endif
      >
    </div>
  </div>
  <div class="col-12 mb-2">
    <div class="form-group">
      <label for="is_asc" class="w-100">
        {{__('labels.other')}}
      </label>
      <label class="mx-2">
      <input type="checkbox" value="1" name="is_asc" class="icheck flat-green"
      @if(isset($filter['sort']['is_asc']) && $filter['sort']['is_asc']==true)
        checked
      @endif
      >{{__('labels.date')}} {{__('labels.asc')}}
      </label>
      <label class="mx-2">
      <input type="checkbox" value="1" name="is_unchecked" class="icheck flat-green"
      @if(isset($filter['sort']['is_unchecked']) && $filter['sort']['is_unchecked']==1)
        checked
      @endif
      >{{__('labels.unchecked_only')}}
      </label>
    </div>
  </div>
  @endslot
@endcomponent

@endsection


@section('page_sidemenu')
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
  <li class="nav-item hr-1">
    <a href="javascript:void(0);" page_title="{{$domain_name}}登録" page_form="dialog" page_url="/{{$domain}}/create" class="nav-link">
      <i class="fa fa-plus nav-icon"></i>{{__('labels.comment_add')}}
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
      <li class="nav-item">
         <a href="/{{$domain}}?search_type=private" class="nav-link @if(isset($search_type) && $index===$search_type) active @endif">
           <i class="fa fa-lock nav-icon"></i>未公開
         </a>
       </li>
       @foreach($attributes['comment_type'] as $index => $name)
      <li class="nav-item">
         <a href="/{{$domain}}?search_type={{$index}}" class="nav-link @if(isset($search_type) && $index===$search_type) active @endif">
           <i class="fa fa-list-alt nav-icon"></i>{{$name}}
         </a>
       </li>
       @endforeach
    </ul>
  </li>
</ul>
@endsection

@section('page_footer')
  <dt>
    <a class="btn btn-app"  href="javascript:void(0);" page_title="{{$domain_name}}登録" page_form="dialog" page_url="{{$domain}}/create">
      <i class="fa fa-plus"></i>{{__('labels.comment_add')}}
    </a>
  </dt>
@endsection
