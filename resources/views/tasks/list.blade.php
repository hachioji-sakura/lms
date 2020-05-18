@extends('dashboard.common')

@section('title_header',__('labels.tasks').__('labels.list'))
@section('title', __('labels.tasks').__('labels.list'))

@section('page_sidemenu')
 @include('tasks.menu')
@endsection

@section('contents')
  @include('tasks.contents')
@endsection

@section('page_footer')
<dt>
  <a href="javascript:void(0)" page_form="dialog" page_title="{{__('labels.tasks').__('labels.add')}}" page_url="/tasks/create" title="{{__('labels.add_button')}}" role="button" class="btn btn-app">
    <i class="fa fa-plus"></i>
    {{__('labels.add_button')}}
  </a>
</dt>
@endsection
