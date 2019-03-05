@section('title')
  {{$domain_name}}一覧
@endsection
@extends('dashboard.common')
@include('dashboard.tiles')

@section('page_sidemenu')
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
  {{--
  @if($user->role=="parent")
  <li class="nav-item hr-1">
    <a href="/{{$domain}}/create" class="nav-link">
      <i class="fa fa-plus nav-icon"></i>{{$domain_name}}登録
    </a>
  </li>
  @endif
  --}}
  <li class="nav-item has-treeview menu-open mt-2">
    <a href="#" class="nav-link">
      <i class="nav-icon fa fa-filter"></i>
      <p>
        フィルタリング
        <i class="right fa fa-angle-left"></i>
      </p>
    </a>
    <ul class="nav nav-treeview">
      <li class="nav-item hr-1">
        @component('components.search_word', ['search_word' => $search_word])
        @endcomponent
      </li>
      @if($user->role==="teacher")
      <li class="nav-item">
        <a href="/{{$domain}}" class="nav-link">
          <i class="fa fa-user-friends nav-icon"></i>担当生徒
        </a>
      </li>
      <li class="nav-item">
        <a href="/{{$domain}}?filter=all" class="nav-link">
          <i class="fa fa-users nav-icon"></i>すべて
        </a>
      </li>
      @elseif($user->role==="manager")
      <li class="nav-item">
        <a href="/{{$domain}}?status=0" class="nav-link">
          <i class="fa fa-user-check nav-icon"></i>登録済み
        </a>
      </li>
      <li class="nav-item">
        <a href="/{{$domain}}?status=1" class="nav-link">
          <i class="fa fa-user-lock nav-icon"></i>仮登録
        </a>
      </li>
      <li class="nav-item">
        <a href="/{{$domain}}?status=9" class="nav-link">
          <i class="fa fa-user-times nav-icon"></i>削除済み
        </a>
      </li>
      <li class="nav-item">
        <a href="/{{$domain}}" class="nav-link">
          <i class="fa fa-users nav-icon"></i>すべて
        </a>
      </li>
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
