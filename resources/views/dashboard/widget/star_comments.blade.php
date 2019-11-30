@section('star_comments')
<div class="col-12 col-md-4 mb-2">
  <div class="card card-widget">
    <div class="card-header">
      <h3 class="card-title">
        <i class="fa fa-thumbtack mr-1"></i>
        {{__('labels.star')}}
        @if($domain!="students")
        {{__('labels.announcements')}}
        @else
        {{__('labels.comments')}}
        @endif
      </h3>
      <div class="card-tools">
        <button type="button" class="btn btn-default btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse">
            <i class="fa fa-minus"></i>
        </button>
      </div>
    </div>
    <div class="card-footer card-comments" id="star_comment_list">
      <?php $is_exist=false; ?>
      @foreach($star_comments["data"] as $comment)
      <?php
        $comment = $comment->details();
        $is_exist = true;
      ?>

      <div class="card-comment">
        <!-- User image -->
        {{--
        <img class="img-circle img-sm" src="{{$comment->create_user->details()->icon}}" alt="User Image">
        --}}
        <div class="">
          <span class="username mb-1">
            <i class="fa fa-marker mr-1"></i>
            {{$comment["type_name"]}}
            {{--
            {{$comment->create_user->details()->name}}
            --}}
          </span>
          {!!nl2br($comment->body)!!}

          @if(!empty($comment->s3_url))
          <br>
          <span class="mr-1 mt-1">
            <a href="{{$comment->s3_url}}" target="_blank">
              <i class="fa fa-link mr-1"></i>
              {{$comment->s3_alias}}
            </a>
          </span>
          @endif
          <br>
          @if($comment->importance > 1)
          <span class="ml-1 text-xs float-right">
            <small class="badge
            @if($comment->importance > 5)
            badge-danger
            @elseif($comment->importance > 1)
            badge-warning
            @endif
             mt-1 mr-1">
              重要度：{{$comment["importance_label"]}}
            </small>
          </span>
          @endif
        </div>
      </div>
      @endforeach
      @if($is_exist == false)
      <div class="alert">
        <h4><i class="icon fa fa-exclamation-triangle"></i>{{__('labels.no_data')}}</h4>
      </div>
      @endif
    </div>
  </div>
</div>
@endsection
@section('comments')
<div class="col-12 col-md-8 mb-2 mb-2">
  <div class="card direct-chat">
    <div class="card-header">
      <h3 class="card-title">
        <i class="fa fa-comment-dots mr-1"></i>
        @if($domain!="students")
        {{__('labels.announcements')}}
        @else
        {{__('labels.comments')}}
        @endif
      </h3>
      <div class="card-tools">
        <a class="btn btn-tool" href="javascript:void(0);" page_form="dialog" page_url="/comments/create?origin={{$domain}}&item_id={{$item->id}}" page_title="{{__('labels.comment_add')}}">
            <i class="fa fa-pen nav-icon"></i>
        </a>
        <a class="btn btn-tool" data-toggle="modal" data-target="#filter_form" id="filter_button">
            <i class="fa fa-filter nav-icon"></i>
        </a>
        <button type="button" class="btn btn-default btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse">
            <i class="fa fa-minus"></i>
        </button>
      </div>
    </div>
    <div class="card-body">
      <div class="direct-chat-messages" id="comment_list">
        <?php $is_exist=false; ?>
        @foreach($comments["data"] as $comment)
        <?php
          $comment = $comment->details();
          $is_exist = true;
        ?>
        <div class="direct-chat-msg
        @if($comment->create_user->details()->role=="student" || $comment->create_user->details()->role=="parent")
         right
        @endif
        ">
          <div class="direct-chat-info clearfix">
            <span class="direct-chat-name float-left">{{$comment->create_user->details()->name}}</span>
          </div>
          <img class="direct-chat-img" src="{{$comment->create_user->details()->icon}}" alt="message user image">
          <div class="direct-chat-text p-2 pb-5">
            <span class="text-sm text-muted float-right mb-1">
              <i class="fa fa-clock"></i>
              {{$comment["created_date"]}}
            </span>
            <br>

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


            <br>
            <span class="float-right mr-1">
              @if($user->role=="manager" || $comment->create_user_id == $user->user_id)
              {{-- 起票者 or 管理者のみ 操作可能 --}}
              <a href="javascript:void(0);" page_title="コメント編集" page_form="dialog" page_url="/comments/{{$comment->id}}/edit?origin={{$domain}}&item_id={{$item->id}}" role="button" class="btn btn-default btn-sm float-left mr-1">
                <i class="fa fa-edit"></i>
              </a>
              <a href="javascript:void(0);" page_title="コメント削除" page_form="dialog" page_url="/comments/{{$comment->id}}?action=delete&domain={{$domain}}&item_id={{$item->id}}" role="button" class="btn btn-default btn-sm float-left mr-1">
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
                  service.postAjax('/comments/'+id+'/importanced',{},
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
                  service.postAjax('/comments/'+id+'/checked',{},
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
        </div>
        @endforeach
        @if($is_exist == false)
        <div class="alert">
          <h4><i class="icon fa fa-exclamation-triangle"></i>{{__('labels.no_data')}}</h4>
        </div>
        @endif
      </div>
    </div>
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
            @if(isset($filter['search_comment_type']) && in_array($index, $filter['search_comment_type'])==true)
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
      @if(isset($filter['is_asc']) && $filter['is_asc']==true)
        checked
      @endif
      >{{__('labels.date')}} {{__('labels.asc')}}
      </label>
      <label class="mx-2">
      <input type="checkbox" value="1" name="is_unchecked" class="icheck flat-green"
      @if(isset($filter['is_unchecked']) && $filter['is_unchecked']==1)
        checked
      @endif
      >{{__('labels.unchecked_only')}}
      </label>
    </div>
  </div>
  @endslot
@endcomponent

@endsection
