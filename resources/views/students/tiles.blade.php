@section('title')
{{__('labels.students_list')}}
@endsection
@extends('dashboard.common')
@include('dashboard.tiles')

@section('page_sidemenu')
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
  {{--
  @if($user->role=="parent")
  <li class="nav-item hr-1">
    <a href="/{{$domain}}/create" class="nav-link">
      <i class="fa fa-plus nav-icon"></i>{{$domain_name}} {{__('labels.add')}}
    </a>
  </li>
  @endif
  --}}
  <li class="nav-item has-treeview menu-open mt-2">
    <a href="#" class="nav-link">
      <i class="nav-icon fa fa-filter"></i>
      <p>
        {{__('labels.filter')}}
        <i class="right fa fa-angle-left"></i>
      </p>
    </a>
    <ul class="nav nav-treeview">
      @if($user->role==="teacher")
      <li class="nav-item">
        <a href="/{{$domain}}" class="nav-link">
          <i class="fa fa-user-friends nav-icon"></i>{{__('labels.charge_student')}}
        </a>
      </li>
      <li class="nav-item">
        <a href="/{{$domain}}" class="nav-link @if(!isset($_status)) active @endif">
          <i class="fa fa-users nav-icon"></i>すべて
        </a>
      </li>
      @elseif($user->role==="manager")
      @foreach(config('attribute.student_status') as $index => $name)
      <li class="nav-item">
        <a href="/{{$domain}}?status={{$index}}" class="nav-link @if((!isset($_status) && $index=='regular') || ($_status==$index)) active @endif">
          <i class="fa fa-list-alt nav-icon"></i>{{$name}}
        </a>
      </li>
      @endforeach
      @endif
    </ul>
  </li>
</ul>
@endsection

@section('page_footer')
{{--
<dt>
  @if($user->role=="parent")
    <a href="/{{$domain}}/create" class="btn btn-app" >
      <i class="fa fa-plus"></i>{{$domain_name}}登録
    </a>
  @endif
</dt>
--}}
@endsection
