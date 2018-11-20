@include('students.domain')
@section('title')
  @yield('domain_name')ダッシュボード
@endsection
@extends('dashboard.common')

@include('teachers.menu.page_sidemenu')
@include('teachers.menu.page_footer')

@include('dashboard.widget.comments')

{{--まだ対応しない
@include('dashboard.widget.milestones')
@include('dashboard.widget.events')
@include('dashboard.widget.tasks')
--}}

@section('contents')
<section id="member" class="content-header">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-4">
        @component('components.profile', ['item' => $item, 'user' => $user, 'use_icons' => $use_icons])
            @slot('courtesy')
            　様
            @endslot
            @slot('alias')
              <h6 class="widget-user-desc">
                <small class="badge badge-secondary mt-1">
                  {{$item->age}}歳
                </small>
                <!--
                  <i class="fa fa-calendar mr-1"></i>yyyy/mm/dd
                  <br>
                  <small class="badge badge-info mt-1">
                    <i class="fa fa-user mr-1"></i>中学1年
                  </small>
                  <small class="badge badge-info mt-1">
                    <i class="fa fa-chalkboard-teacher mr-1"></i>XXコース
                  </small>
              -->
              </h6>
              {{--
                <div class="card-footer p-0">
                  <ul class="nav flex-column">
                    <li class="nav-item">
                      <a href="#comments" class="nav-link">
                        コメント
                        <span class="float-right badge bg-danger">99</span>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="#events" class="nav-link">
                        イベント
                        <span class="float-right badge bg-danger">99</span>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="#tasks" class="nav-link">
                        タスク
                        <span class="float-right badge bg-danger">99</span>
                      </a>
                    </li>
                  </ul>
                </div>
              --}}
            @endslot
        @endcomponent
			</div>
			<div class="col-md-8">
				@yield('comments')
			</div>
		</div>
	</div>
</section>

{{--まだ対応しない
<section class="content-header">
	<div class="container-fluid">
		<div class="row">
			<div class="col-12 col-lg-6 col-md-6">
				@yield('milestones')
			</div>
			<div class="col-12 col-lg-6 col-md-6">
				@yield('events')
			</div>
		</div>
	</div>
</section>

<section class="content">
	@yield('tasks')
</section>
--}}


@endsection
