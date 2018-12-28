@section('title')
  {{$domain_name}}ダッシュボード
@endsection
@extends('dashboard.common')
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
        @component('components.profile', ['item' => $item, 'user' => $user, 'use_icons' => $use_icons, 'domain' => $domain, 'domain_name' => $domain_name])
            @slot('courtesy')
            　様
            @endslot
            @slot('alias')
              <h6 class="widget-user-desc">
                @foreach($item["tags"] as $tag)
                <small class="badge badge-secondary mt-1 mr-1">
                  {{$tag->name()}}
                </small>
                @endforeach
              </h6>
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



@section('page_sidemenu')
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    <li class="nav-item has-treeview menu-open">
      <a href="#" class="nav-link">
      <i class="nav-icon fa fa-user"></i>
      <p>
        <ruby style="ruby-overhang: none">
          <rb>{{$item->name}}</rb>
          <rt>{{$item->kana}}</rt>
        </ruby>
        <i class="right fa fa-angle-left"></i>
      </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item">
          <a class="nav-link" href="/{{$domain}}/{{$item->id}}/" >
            <i class="fa fa-home nav-icon"></i>HOME
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/{{$domain}}/{{$item->id}}/calendar" >
            <i class="fa fa-calendar nav-icon"></i>授業予定
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/{{$domain}}/{{$item->id}}/calendar?mode=list" >
            <i class="fa fa-calendar nav-icon"></i>欠席連絡
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="javascript:void(0);"  page_form="dialog" page_url="/comments/create?_page_origin={{$domain}}_{{$item->id}}&student_id={{$item->id}}" page_title="目標登録">
            <i class="fa fa-comment-dots nav-icon"></i>コメント登録
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="javascript:void(0);"  page_form="dialog" page_url="/milestones/create?_page_origin={{$domain}}_{{$item->id}}&student_id={{$item->id}}" page_title="目標登録">
            <i class="fa fa-flag nav-icon"></i>目標登録
          </a>
        </li>
      </ul>
    </li>
</ul>
@endsection
@section('page_footer')
<dt>
  <a class="btn btn-app" href="javascript:void(0);" page_form="dialog" page_url="/comments/create?_page_origin={{$domain}}_{{$item->id}}&student_id={{$item->id}}" page_title="コメント登録">
    <i class="fa fa-comment-dots"></i>コメント登録
  </a>
</dt>
<dt>
  <a class="btn btn-app" href="javascript:void(0);"  page_form="dialog" page_url="/milestones/create?_page_origin={{$domain}}_{{$item->id}}&student_id={{$item->id}}" page_title="目標登録">
    <i class="fa fa-flag"></i>目標登録
  </a>
</dt>
{{-- まだ対応しない
  <dt>
    <a class="btn btn-app" href="javascript:void(0);" accesskey="task_add" disabled>
      <i class="fa fa-plus"></i>タスク登録
    </a>
  </dt>
--}}
@endsection
