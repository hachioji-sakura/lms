@extends('dashboard.common')

@section('title_header',$domain_name)
@section('title',$domain_name)

@section('page_sidemenu')
 @include('tasks.menu')
@endsection

@section('contents')
  @include('tasks.contents')
@endsection

@section('page_footer')
<dt>
  <a href="javascript:void(0)" page_form="dialog" page_title="{{__('labels.tasks').__('labels.add')}}" page_url="/tasks/create?student_id={{$target_user->id}}" title="{{__('labels.add_button')}}" role="button" class="btn btn-app">
    <i class="fa fa-plus"></i>
    {{__('labels.add_button')}}
  </a>
</dt>
@endsection
