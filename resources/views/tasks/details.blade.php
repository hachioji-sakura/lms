@extends('dashboard.common')

@section('title', $item->target_user->details()->name().'さんの'.$domain_name)

@section('contents')
<div class="card">
  <div class="card-header">
    <small class="badge badge-{{config('attribute.task_status')['style'][$item->status]}}">
      {{config('attribute.task_status')['status'][$item->status]}}
    </small>
    <div class="row mt-1">
      <div class="col-8 text-truncate">
        <h3 class="card-title text-truncate">
          {{$item->title}}
        </h3>
      </div>
      <div class="col-4">
        @if(isset($item->s3_url))
        <a href="{{$item->s3_url}}" class="float-right">
          <i class="fa fa-link mr-1"></i>
          {{$item->s3_alias}}
        </a>
        @endif
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <small class="text-muted">
          {{$item->create_user->details()->name()}}
          @if( isset($item->milestone_id))
          {{$item->milestones->title}}
          @endif
          @if( isset($item->start_date) && isset($item->end_date) )
          {{$item->start_date}}~{{$item->end_date}}
          @endif
        </small>
      </div>
    </div>
    <div class="card-tools float-right">
      <div class="btn-group">
        @if($item->status == 'new' && $item->target_user_id == $user->user_id )
        <form method="POST" action="/tasks/{{$item->id}}/progress">
          @csrf
          @method('PUT')
          <button type="submit" title="開始する" class="btn btn-sm btn-submit btn-primary mr-1"><i class="fa fa-play"></i></button>
        </form>
        @endif
        @if($item->status == 'progress' && $item->target_user_id == $user->user_id)
        <form method="POST" action="/tasks/{{$item->id}}/done">
          @csrf
          @method('PUT')
          <button type="submit" title="完了する" class="btn btn-sm btn-submit btn-success mr-1"><i class="fa fa-stop"></i></button>
        </form>
        @endif
        @if($item->status == "done" && $item->create_user_id == $user->user_id)
        <a href="javascript:void(0)" title="評価する" page_form="dialog" page_title="{{$item->title}}の評価" page_url="/tasks/{{$item->id}}/review" class="btn btn-sm btn-warning mr-1" role="button">
        <i class="fas fa-pen"></i>
        </a>
        @endif
        @if( $item->status != "complete" && $item->status != "done")
        <a href="javascript:void(0)" page_title="{{$item->title}}" page_form="dialog" page_url="/tasks/{{$item->id}}/edit" class="btn btn-sm btn-info mr-1" role="button">
          <i class="fa fa-edit"></i>
        </a>
        @endif
        @if( ($item->status == "new" || $item->status == "progress") && $item->create_user_id == $user->user_id )
        <a href="javascript:void(0)" page_title="「{{$item->title}}」をキャンセルしますか？" page_form="dialog" page_url="/tasks/{{$item->id}}/cancel" class="btn btn-sm btn-danger mr-1" role="button">
          <i class="fa fa-trash"></i>
        </a>
        @endif
      </div>
    </div>
  </div>

  <div class="card-body">
    @if(isset($item->remarks))
    <div class="row mt-2">
      <div class="col-12">
        <label for="contents" class="w-100">
          {{__('labels.contents')}}
           <button type="button" class="btn btn-tool" data-toggle="collapse" data-target="#task_details"><i class="fas fa-plus"></i></button>
        </label>
      </div>
    </div>
    <div class="collapse" id="task_details">
      <div class="row">
        <div class="col-12">
          <div class="form-group">
              {!!nl2br($item->remarks)!!}
          </div>
        </div>
      </div>
    </div>
    @endif
    @if($item->reviews->count() > 0)
    <div class="row mt-2">
      <div class="col-12">
        <label for="review" class="w-100">
          {{__('labels.review')}}
           <button type="button" class="btn btn-tool" data-toggle="collapse" data-target="#reviews"><i class="fas fa-plus"></i></button>
        </label>
      </div>
    </div>
    <div class="collapse" id="reviews">
      <div class="row">
        @foreach($item->reviews as $review)
        <div class="col-12">
          <div class="callout callout-warning">
            <h4>{{$review->create_user->details()->name()}}</h4>
            @for($i=1;$i<=$item->evaluation;$i++)
            <span class="fa fa-star" style="color:orange;"></span>
            @endfor
            <p>{!!nl2br($review->body)!!}</p>
            <small class="text-muted">{{$review->created_at}}</small>
          </div>
        </div>
        @endforeach
      </div>
    </div>
    @endif
  </div>
  <div class="card-footer">
    <div class="card-bordered">
      <div class="card-header">
        <label for="comments" class="w-100">
          {{__('labels.comments')}}
        </label>
      </div>
      <form method="post" action="/task_comments/create" enctype="multipart/form-data">
        @csrf
        <div class="input-group mb-3">
          <input type="hidden" name="task_id" value="{{$item->id}}">
          <input type="text" name="body" class="form-control" placeholder="コメントを入力してください。">
          <span class="input-group-append">
            <button type="submit" class="input-group-text">
              <i class="fas fa-comment mr-1"></i>
              投稿する
            </button>
          </span>
        </div>
        <input type="file" name="upload_file" class="form-control">
      </form>
      @component('components.comments', [
        'comments' => $item->task_comments,
        'comment_type' => 'all',
        'is_active' => 'active',
        'is_edit' => false
      ])
      @endcomponent

    </div>
  </div>
</div>
@endsection
