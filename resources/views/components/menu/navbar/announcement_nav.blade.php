@section('announcement_nav')
@if($user->role!="student")
<?php
$uncheck_comments = $user->user->get_comments(['is_unchecked'=>1]);
$all_comments = $user->user->get_comments([]);
 ?>
<li class="nav-item dropdown">
  <a class="nav-link" data-toggle="dropdown" href="#">
    <i class="fa fa-bell"></i>
    @if($uncheck_comments["count"] > 0)
    <span class="badge badge-danger navbar-badge">{{$uncheck_comments["count"]}}</span>
    @endif
  </a>
  <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
    {{--
    <span class="dropdown-item dropdown-header">
    </span>
    --}}
    <div class="dropdown-divider"></div>
    <a href="javascript:void(0);" class="dropdown-item"  page_title="{{__('labels.unchecked_announcements')}}" page_form="dialog" page_url="/{{$user["domain"]}}/{{$user->id}}/announcements?is_unchecked=1" >
      {{__('labels.unchecked_announcements')}}
      @if($uncheck_comments["count"] > 0)
      <span class="float-right text-muted text-sm">
        {{$uncheck_comments["count"]}}件
      </span>
      @endif
    </a>
    <a href="javascript:void(0);" class="dropdown-item"  page_title="{{__('labels.all_announcements')}}" page_form="dialog" page_url="/{{$user["domain"]}}/{{$user->id}}/announcements" >
      {{__('labels.all_announcements')}}
      {{--
      <span class="float-right text-muted text-sm">
        {{$user->user->get_comments(['is_unchecked'=>1])["count"]}}
      </span>
      --}}
    </a>
    {{--
    <div class="dropdown-divider"></div>
    <a href="#" class="dropdown-item dropdown-footer">
      <i class="fa fa-arrow-right mr-1"></i>{{__('labels.all_announcements')}}
    </a>
    --}}
  </div>
</li>
@endif
@endsection
