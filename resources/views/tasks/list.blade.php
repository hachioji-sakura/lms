@extends('dashboard.common')

@section('title_header',$domain_name)
@section('title',$domain_name)

@section('contents')
<div class="card">
  <div class="card-header">
    <div class="row">
      <div class="col-8">
        @if(isset($target_user))
        <h3 class="card-title">{{$target_user->details()->name()}}さんのタスク</h3>
        @else
        <h3 class="card-title">タスク一覧</h3>
        @endif
      </div>
      <div class="col-4">
        @if(isset($target_user))
        <div class="float-right">
          <a href="javascript:void(0)" page_form="dialog" page_title="For {{$target_user->details()->name}}" page_url="/{{$target_user->details()->domain}}/{{$target_user->id}}/create_tasks" title="{{__('labels.add_button')}}" role="button"  class="btn btn-primary">
            <i class="fa fa-plus"></i>
          </a>
        </div>
        @endif
      </div>
    </div>
  </div>
  <div class="card-body p-0">
    <ul class="products-list product-list-in-card pl-2 pr-2">
      @foreach($items as $item)
      <li class="item {{$item->status == "cancel" ? 'bg-info' : ''}}">
        <div class="row">
          <div class="col-3 text-truncate">
            <a href="/tasks/{{$item->id}}" title="{{__('labels.details')}}">
              <i class="fa fa-tag"></i>
              {{$item->title}}
            </a>
          </div>
          <div class="col-2">
            <small class="badge badge-{{config('attribute.task_status')['style'][$item->status]}}">
              {{config('attribute.task_status')['status'][$item->status]}}
            </small>
          </div>
          <div class="col-4 text-truncate">
            <small class="text-muted">
              {{$item->remarks}}
            </small>
          </div>
          <div class="col-3">
            <small class="badge badge-danger float-left">
              <i class="fas fa-stopwatch mr-1"></i>{{$item->end_schedule}}
            </small>
          </div>
        </div>
      </li>
      @endforeach
    </ul>
  </div>
</div>
<script>
$(function(){
  base.pageSettinged("delete_task");
  $('button.btn-submit[form="delete_task"]').on('click', function(e){
    e.preventDefault();
    if(front.validateFormValue('delete_task')){
      $("form#delete_task").submit();
    }
  });
})

</script>
@endsection
