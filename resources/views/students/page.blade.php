@extends('dashboard.common')
@include('dashboard.menu.navbar')
@include('students.page.sidemenu')
@include('students.page.footer')

@section('title', 'ダッシュボード')

@include('dashboard.widget.profile')
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
				@yield('profile')
				{{--
				@include('components.profile', [
					'profile_id'=>$item->id,
					'profile_name'=>$item->name_last.' '.$item->name_first,
					'profile_kana'=>$item->kana_last.' '.$item->kana_first,
					'profile_icon'=>$item->icon,
					'profile_age'=>$item->age
					])
				--}}
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
