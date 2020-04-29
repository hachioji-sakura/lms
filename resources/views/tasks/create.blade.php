@extends('dashboard.common')

@section('title_header',$domain_name.'タスク登録')
@section('title',$domain_name.'タスク登録')


@section('contents')
<div class="card">
  <!--
  <div class="card-header">
    <h3 class="card-title">{{__('labels.add_tasks')}}</h3>
  </div>
  -->
  <div class="card-body">
    <div id="create_tasks" class="form-group">
      @if($_edit)
      <form method="POST" action="/tasks/{{$item->  id}}/edit">
        @method('PUT')
      @else
      <form method="POST" action="/tasks/create">
      @endif
        @csrf
        <div class="row">
          <div class="col-6">
            <label>{{__('labels.target_user')}}</label>
            @if(!isset($target_user))
            <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
            <select name="type" class="form-control">
              <option value="hoge">hoge</option>
            </select>
            @else
            <div class="row">
              <div class="col-12">
                {{$target_user->details()->name()}}
                <input type="hidden" name="target_user_id" value="{{$target_user->details()->user->id}}">
              </div>
            </div>
            @endif
          </div>

          <div class="col-6">
            <label>{{__('labels.type')}}</label>
            <span class="right badge badge-primary ml-1">{{__('labels.optional')}}</span>
            <select name="type" class="form-control">
              <option value="hoge">hoge</option>
            </select>
          </div>
        </div>

        <div class="row mt-2">
          <div class="col-12">
            <label>{{__('labels.title')}}</label>
            <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
            <input type="text" class="form-control" name="title" placeholder="{{__('labels.title')}}" required="true" value="{{$_edit ? $item->title : ''}}">
          </div>
        </div>
        <div class="row mt-2">
          <div class="col-12">
            <label>{{__('labels.tasks_remarks')}}</label>
            <span class="right badge badge-primary ml-1">{{__('labels.optional')}}</span>
            <textarea name="remarks" class="form-control" placeholder="{{__('labels.tasks_remarks')}}" >{{$_edit ? $item->remarks : ''}}</textarea>
          </div>
        </div>
        <!--
        <div class="row mt-2">
          <div class="col-6">
            <label>{{__('labels.status')}}</label>
            <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
            <select name="status" class="form-control">
              <option value="hoge">hoge</option>
            </select>
          </div>
        </div>
      -->
        <div class="row mt-2">
          <div class="col-6">
            <label>{{__('labels.start_schedule')}}</label>
            <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
            <input type="text" name="start_schedule" class="form-control" required="true" uitype="datepicker" minvalue="{{date('Y/m/d')}}"   plaminceholder=""  value="{{$_edit ? $item->start_schedule : date('Y/m/d')}}">
          </div>
          <div class="col-6">
            <label>{{__('labels.end_schedule')}}</label>
            <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
            <input type="text" name="end_schedule" class="form-control" required="true" uitype="datepicker" minvalue="{{date('Y/m/d')}}" plaminceholder=""  value="{{$_edit ? $item->end_schedule : date('Y/m/d', strtotime("7 day"))}}">
          </div>
        </div>
        <div class="row mt-2">
          <div class="col-12">
            <label>{{__('labels.milestones')}}</label>
            <span class="right badge badge-primary ml-1">{{__('labels.optional')}}</span>
            <select name="milestone_id" class="form-control select2" width="100%">
              <option value=" ">選択しない</option>
              @foreach($target_user->target_milestone as $milestone)
                <option value="{{$milestone->id}}" {{$_edit && $milestone->id == $item->milestone_id ? 'selected ': ''}}>{{$milestone->title}}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="row mt-2">
          <div class="col-12">
            <button type="submit" class="btn btn-submit btn-primary btn-block"><i class="fa {{$_edit ? 'fa-edit':'fa-plus-circle'}} mr-1"></i>{{__('labels.add_button')}}</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
$(function(){
  base.pageSettinged("create_tasks",null)
})
</script>
@endsection
