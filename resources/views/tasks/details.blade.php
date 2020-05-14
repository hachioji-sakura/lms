@extends('dashboard.common')

@section('title', $item->target_user->details()->name().'さんの'.$domain_name)
@section('title_header', $item->target_user->details()->name().'さんの'.$domain_name)

@section('page_sidemenu')
  @include('tasks.menu')
@endsection

@section('page_footer')
  @foreach($buttons as $key => $value)
      <dt>
        <a href="javascript:void(0)" title="{{$value}}" page_form="dialog" page_title="{{$key == 'complete' ? __('messages.task_review_confirm'): __('messages.task_confirm')}}" page_url="/tasks/{{$item->id}}/{{$key}}" class="btn btn-app" role="button">
        <i class="fa fa-{{config('attribute.status_icon')[$key]}}"></i>
        <small class="text-sm">
        {{$value}}
        </small>
        </a>
      </dt>
  @endforeach
@endsection


@section('contents')
<div class="card">
  <div class="card-header">
    <small class="badge badge-{{config('status_style')[$item->status]}}">
      {{config('attribute.task_status')[$item->status]}}
    </small>
    <div class="row mt-1">
      <div class="col-12">
        <h3 class="card-title">
          {{$item->title}}
        </h3>
        @if(!empty($item->evaluation))
        @for($i=1;$i<=$item->evaluation;$i++)
        <span class="fa fa-star" style="color:orange;"></span>
        @endfor
        @for($i=1;$i<=5-$item->evaluation;$i++)
        <span class="far fa-star"></span>
        @endfor
        ({{$item->evaluation}})
        @endif
        @if(!empty($item->s3_url))
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
      <a href="/{{$domain}}/{{$target_user->id}}/tasks" class="btn btn-sm btn-primary">
        <i class="fa fa-arrow-left"></i>
        {{__('labels.back_button')}}
      </a>
    </div>
  </div>
  <div class="d-none d-sm-block">
    <div class="card-header">
      <div class="btn-group">
        @foreach($buttons as $key => $value)
          <a href="javascript:void(0)" title="{{$value}}" page_form="dialog" page_title="{{__('messages.task_confirm')}}" page_url="/tasks/{{$item->id}}/{{$key}}" class="btn btn-sm btn-{{config('status_style')[$key]}} mr-1" role="button">
          <i class="fa fa-{{config('attribute.status_icon')[$key]}}"></i>
          {{$value}}
          </a>
        @endforeach
      </div>
    </div>
  </div>

  <div class="card-body">
    @if(isset($item->body))
    <div class="row">
      <div class="col-12">
        <div class="form-group">
            {!!nl2br($item->body)!!}
        </div>
      </div>
    </div>
    <!--詳細の折り畳みレイアウト
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
              {!!nl2br($item->body)!!}
          </div>
        </div>
      </div>
    </div>
  -->
    @endif
    <!--
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
            <p>{!!nl2br($review->body)!!}</p>
            <small class="text-muted">{{$review->created_at}}</small>
          </div>
        </div>
        @endforeach
      </div>
    </div>
    @endif
  </div>
  -->
  <!--
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
-->
</div>

@endsection
