@section('title')
  {{$domain_name}}ダッシュボード
@endsection
@extends('teachers.page')
@include($domain.'.menu')

@section('sub_contents')
<div class="row">
  <div class="col-12 col-lg-4 col-md-6 mb-1">
    <a class="" href="/student_groups?teacher_id={{$item->id}}" >
    <div class="info-box">
      <span class="info-box-icon bg-secondary">
        <i class="fa fa-users"></i>
      </span>
      <div class="info-box-content text-dark">
        <b class="info-box-text text-lg">{{__('labels.student_groups')}}</b>
        <span class="text-sm">{{__('labels.student_groups_description')}}</span>
      </div>
    </div>
    </a>
  </div>
  <div class="col-12 col-lg-4 col-md-6 mb-1">
    <a class="" href="javascript:void(0);" page_form="dialog" page_url="/{{$domain}}/{{$item->id}}/edit" page_title="{{__('labels.teacher_setting')}}">
    <div class="info-box">
      <span class="info-box-icon bg-secondary">
        <i class="fa fa-user-edit"></i>
      </span>
      <div class="info-box-content text-dark">
        <b class="info-box-text text-lg">{{__('labels.teacher_setting')}}</b>
        <span class="text-sm">{{__('labels.teacher_setting_description')}}</span>
      </div>
    </div>
    </a>
  </div>
  <div class="col-12 col-lg-4 col-md-6 mb-1">
    <a class="" href="javascript:void(0);" page_form="dialog" page_url="/{{$domain}}/{{$item->id}}/setting" page_title="{{__('labels.working')}}{{__('labels.setting')}}">
    <div class="info-box">
      <span class="info-box-icon bg-secondary">
        <i class="fa fa-business-time"></i>
      </span>
      <div class="info-box-content text-dark">
        <b class="info-box-text text-lg">{{__('labels.working')}}{{__('labels.setting')}}</b>
        <span class="text-sm">{{__('labels.working_setting_description')}}</span>
      </div>
    </div>
    </a>
  </div>
</div>
@endsection
