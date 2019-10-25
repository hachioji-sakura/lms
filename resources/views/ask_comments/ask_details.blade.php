@section('title')
{{__('labels.ask_list')}}
@endsection
@extends('dashboard.common')

@section('contents')
<section class="content">
<div class="row">
  <div class="card card-info card-widget col-12 col-md-6 mt-1">
    <div class="card-header">
      <div class="user-block">
        <i class="fa fa-phone mx-1 float-left" style="font-size:24px;"></i>
        <span class="username">
          依頼詳細
        </span>
      </div>
      <!-- /.user-block -->
      <div class="card-tools">
        <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i>
        </button>
      </div>
      <!-- /.card-tools -->
    </div>
    <!-- /.card-header -->
    <div class="card-body row">
      <div class="col-6">
        <b>依頼概要</b>
        <p class="ml-3">
          {{$ask->type_name()}}
        </p>
      </div>
      <div class="col-6">
        <b>ステータス</b>
        <p class="ml-3">
          <small class="badge badge-{{config('status_style')[$ask->status]}} mt-1 mr-1">
            {{$ask->status_name()}}
          </small>
        </p>
      </div>
      <div class="col-12">
        <b>内容</b>
        <p class="box-text p-3 m-1"  style="min-height:240px;">
          {!!nl2br($ask->body)!!}
        </p>
      </div>
      <div class="col-12">
        <b>起票者</b>
        <p class="ml-3">
          {{$ask["create_user_name"]}}
        </p>
      </div>
      <div class="col-12">
        <b>起票日</b>
        <p class="ml-3">
          {{$ask->created_at_label('Y年m月d日')}}
        </p>
      </div>
      <div class="col-12 text-right">
        <a title="{{$ask["id"]}}" href="javascript:void(0);" page_title="依頼内容編集" page_form="dialog" page_url="/parents/{{$item->id}}/ask/{{$ask->id}}/edit" role="button" class="btn btn-success btn-sm">
          <i class="fa fa-edit mr-1"></i>
          編集
        </a>
      </div>
    </div>
  </div>
  <div class="card card-widget col-12 col-md-6 mt-1">
    <div class="card-header">
      <div class="user-block">
        <i class="fa fa-comment-dots mx-1 float-left" style="font-size:24px;"></i>
        <span class="username">
          コメント一覧
        </span>
        <div class="description">
          <span >
            コメント数:{{count($ask->comments)}}
          </span>
          <a title="{{$ask["id"]}}" href="javascript:void(0);" page_title="依頼へのコメント" page_form="dialog" page_url="/asks/{{$ask->id}}/comments/create" role="button" class="btn btn-outline-primary btn-sm float-right">
            <i class="fa fa-comment-dots mr-1"></i>
            コメント登録
          </a>
        </div>
      </div>
      <!-- /.user-block -->
      <div class="card-tools">
        <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i>
        </button>
      </div>
      <!-- /.card-tools -->
    </div>
    <!-- /.card-header -->
    <div class="card-body">
      <div class="direct-chat-messages" id="comment_list">
        <?php $is_exist=false; ?>
        @foreach($ask->comments->sortByDesc('created_at') as $comment)
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
                <a href="javascript:void(0);" page_title="コメント編集" page_form="dialog" page_url="/asks/{{$ask->id}}/comments/{{$comment->id}}/edit?origin={{$domain}}&item_id={{$item->id}}" role="button" class="btn btn-default btn-sm float-left mr-1">
                  <i class="fa fa-edit"></i>
                </a>
                <a href="javascript:void(0);" page_title="コメント削除" page_form="dialog" page_url="/ask_comments/{{$comment->id}}?action=delete&domain={{$domain}}&item_id={{$item->id}}" role="button" class="btn btn-default btn-sm float-left mr-1">
                  <i class="fa fa-trash"></i>
                </a>
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
    <!-- /.card-body -->
  </div>
  </div>
</div>
</section>
@endsection


@section('page_footer')
<dt>
  <a class="btn btn-app" href="javascript:void(0);" page_form="dialog" page_form="dialog" page_url="/parents/{{$item->id}}/ask/{{$ask->id}}/edit" page_title="依頼内容編集">
    <i class="fa fa-edit"></i>依頼編集
  </a>
</dt>
<dt>
  <a class="btn btn-app" href="javascript:void(0);" page_form="dialog" page_form="dialog" page_url="/asks/{{$ask->id}}/comments/create" page_title="依頼へのコメント">
    <i class="fa fa-comment-dots"></i>コメント登録
  </a>
</dt>
@endsection
