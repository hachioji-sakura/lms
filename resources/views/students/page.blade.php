@section('title')
  {{$domain_name}}ダッシュボード
@endsection
@extends('dashboard.common')
@include($domain.'.menu')
@include('dashboard.widget.milestones')

@include('dashboard.widget.comments')

{{--まだ対応しない
@include('dashboard.widget.events')
@include('dashboard.widget.tasks')
--}}

@section('contents')
<section id="member" class="content-header">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-4">
        @component('components.profile', ['item' => $item, 'user' => $user, 'domain' => $domain, 'domain_name' => $domain_name])
            @slot('courtesy')
            　様
            @endslot
            @slot('alias')
              <h6 class="widget-user-desc">
                <small class="badge badge-dark mt-1 mr-1">
                  No.{{$item->tag_name('student_no')}}
                </small>
                <small class="badge badge-{{config('status_style')[$item->status]}} mt-1 mr-1">
                  {{$item->status_name()}}
                </small>
                @if($item->is_juken()==true)
                <small class="badge badge-warning mt-1 mr-1" title="">
                  受験生
                </small>
                @endif
                @if($item->is_fee_free()==true)
                <small class="badge badge-secondary mt-1 mr-1" title="">
                  受講料無料
                </small>
                @endif
                @if($item->is_arrowre()==true)
                <small class="badge badge-secondary mt-1 mr-1" title="">
                  アローレ所属
                </small>
                @endif
                <small class="badge badge-primary mt-1 mr-1">
                  {{$item->gender()}}
                </small>
                <small class="badge badge-primary mt-1 mr-1">
                  {{$item->grade()}}
                </small>
                @if(!empty($item->school_name()))
                <small class="badge badge-primary mt-1 mr-1">
                  {{$item->school_name()}}
                </small>
                @endif
                @foreach($item->user->tags as $tag)
                @if($user->role==="manager" && $tag->tag_key=="student_character")
                  <small class="badge badge-info mt-1 mr-1">
                    {{$tag->name()}}
                  </small>
                @endif
                @endforeach
              </h6>
              {{--
              <div class="card-footer p-0">
                <ul class="nav flex-column">
                  <li class="nav-item">
                    <a href="/examinations" class="nav-link">
                      <i class="fa fa-file-signature mr-2"></i>
                      確認テスト
                      <span class="float-right badge bg-danger">New</span>
                    </a>
                  </li>
                </ul>
              </div>
              --}}
            @endslot
        @endcomponent
			</div>
			<div class="col-md-8">
        @yield('milestones')
			</div>
		</div>
	</div>
</section>


<section class="content-header">
	<div class="container-fluid">
		<div class="row">
			<div class="col-12 ">
				@yield('comments')
			</div>

		</div>
	</div>
</section>

@endsection
