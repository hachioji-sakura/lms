@section('star_comments')
<div class="card card-widget mb-2 col-12 col-md-4">
  <div class="card-header">
    <h3 class="card-title">
      <i class="fa fa-thumbtack mr-1"></i>重要{{__('labels.comments')}}
    </h3>
    <div class="card-tools">
      <button type="button" class="btn btn-default btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse">
          <i class="fa fa-minus"></i>
      </button>
    </div>
  </div>
  <!-- /.card-body -->
  <div class="card-footer card-comments">
    @foreach($star_comments["data"] as $comment)
    <?php $comment = $comment->details(); ?>
    <!-- /.card-comment -->
    <div class="card-comment">
      <!-- User image -->
      {{--
      <img class="img-circle img-sm" src="{{$comment->create_user->details()->icon}}" alt="User Image">
      --}}
      <div class="">
        <span class="username mb-1">
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
  </div>
</div>
@endsection

@section('comments')
<div class="card direct-chat mb-2 col-12 col-md-8">
  <div class="card-header">
    <h3 class="card-title">
      <i class="fa fa-comment-dots mr-1"></i>{{__('labels.comments')}}
    </h3>
    <div class="card-tools">
      <a class="btn btn-tool" href="javascript:void(0);" page_form="dialog" page_url="/comments/create?origin=students&amp;item_id=357" page_title="コメント登録">
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
  <!-- /.card-header -->
  <div class="card-body">
    <div class="direct-chat-messages" id="comment_list">
      @foreach($comments["data"] as $comment)
      <?php $comment = $comment->details(); ?>
      <div class="direct-chat-msg ">
        <div class="direct-chat-info clearfix">
            <span class="direct-chat-name float-left">{{$comment->create_user->details()->name}}</span>
        </div>
        <img class="direct-chat-img" src="{{$comment->create_user->details()->icon}}" alt="message user image">
        <div class="direct-chat-text p-2 pb-5">
          <span class="text-sm text-muted float-right mb-1">{{$comment["created_date"]}}</span>
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


          @if($user->user_id === $comment->create_user_id)
          <br>
          <span class="float-right mr-1">
            <a href="javascript:void(0);" page_title="コメント編集" page_form="dialog" page_url="/comments/{{$comment->id}}/edit?origin={{$domain}}&item_id={{$item->id}}" role="button" class="btn btn-default btn-sm float-left mr-1">
              <i class="fa fa-edit"></i>
            </a>
            <a href="javascript:void(0);" page_title="コメント削除" page_form="dialog" page_url="/comments/{{$comment->id}}?action=delete&domain={{$domain}}&item_id={{$item->id}}" role="button" class="btn btn-default btn-sm float-left mr-1">
              <i class="fa fa-trash"></i>
            </a>
          </span>
          @endif



        </div>
      </div>
      @endforeach
    </div>
    <!-- /.card-body -->
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
  <div class="col-12 col-md-4 mb-2">
    <div class="form-group">
      <label for="is_exchange" class="w-100">
        {{__('labels.sort_no')}}
      </label>
      <label class="mx-2">
      <input type="checkbox" value="1" name="is_asc" class="icheck flat-green"
      @if(isset($filter['is_asc']) && $filter['is_asc']==true)
        checked
      @endif
      >{{__('labels.date')}} {{__('labels.asc')}}
      </label>
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
  @endslot
@endcomponent

@endsection
