@section('title')
  {{$domain_name}}ダッシュボード
@endsection
@extends('dashboard.common')

@include('dashboard.widget.comments')
@include('dashboard.widget.milestones')

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
        @yield('milestones')
			</div>
		</div>
	</div>
</section>

<section class="content-header">
	<div class="container-fluid">
		<div class="row">
			<div class="col-12">
        @yield('comments')
			</div>
      {{--まだ対応しない
			<div class="col-12 col-lg-6 col-md-6">
				@yield('events')
			</div>
      --}}
		</div>
	</div>
</section>

{{--まだ対応しない
<section class="content">
	@yield('tasks')
</section>
--}}
@endsection

@section('page_sidemenu')
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    <li class="nav-item has-treeview menu-open">
      <a href="#" class="nav-link">
      <i class="nav-icon fa fa-user-graduate"></i>
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
          <a href="javascript:void(0);" class="nav-link" accesskey="comment_add">
            <i class="fa fa-comment-dots nav-icon"></i>コメント登録
          </a>
        </li>
        <li class="nav-item">
          <a href="javascript:void(0);" class="nav-link" accesskey="milestone_add">
            <i class="fa fa-flag nav-icon"></i>目標登録
          </a>
        </li>
      </ul>
    </li>
</ul>
@endsection

@section('page_footer_form')
<div id="footer_form" class="card card-primary card-outline collapse footer-form">
  <div class="card-header">
    <h3 class="card-title"></h3>
  </div>
  <div class="card-body">
  </div>
</div>
<script>
$(function(){
  @if((env('APP_DEBUG')))
  var milestone_data = [
    {
      "milestone_title" : "工学院大学建築学科に合格する",
      "milestone_body" : "ひとまず、AO入試を目標とし、\nB日程に向けて学習を進める",
      "milestone_type" : "promotion",
    },
    {
      "milestone_title" : "センター試験の理系科目70%",
      "milestone_body" : "工学院大学A日程に向け\n必要となるセンター科目・物理・数１A・数２Bを７０点以上取る",
      "milestone_type" : "study",
    },
    {
      "milestone_title" : "都大会３位以上に入賞する",
      "milestone_body" : "部活の秋最後の大会に入賞する。\n推薦入試にも多分有利に働くはず",
      "milestone_type" : "other",
    }
  ];
  base.pageSettinged("milestone_add", milestone_data[(Math.random()*100|0) % milestone_data.length]);
  @else
  base.pageSettinged("milestone_add", null);
  @endif
});
$(function(){
  $("footer.main-footer a.btn[page_url][page_title][page_form='footer_form'], a.nav-link[page_url][page_title][page_form='footer_form']").on("click", function(e){
      $("#footer_form .card-title").html($(this).attr("page_title"));
      $('#footer_form .card-body').load($(this).attr("page_url"), function(){
        base.pageSettinged('footer_form form', null);
        $('.footer-form.show').collapse('hide');
        //サブページ内のsubmit
        $("#footer_form .btn[type=submit][accesskey]").on("click", function(){
          var form = "footer_form .card-body form";
          if(!front.validateFormValue(form)) return false;
          $("#"+form).submit();
        });
        //サブページ内のreset
        $("#footer_form .btn[type=reset]").on("click", function(){
          $('#footer_form').collapse('hide');
        });
        $('#footer_form').collapse('show');
      });
  });
});
</script>
@endsection

@section('page_footer')
<dt>
  <a class="btn btn-app" href="javascript:void(0);" accesskey="students_edit_form" page_form="footer_form" page_url="/comments/create?_page_origin={{$domain}}_{{$item->id}}&student_id={{$item->id}}" page_title="コメント登録">
    <i class="fa fa-comment-dots"></i>コメント登録
  </a>
</dt>
<dt>
  <a class="btn btn-app" href="javascript:void(0);"  accesskey="students_edit_form" page_form="footer_form" page_url="/milestones/create?_page_origin={{$domain}}_{{$item->id}}&student_id={{$item->id}}" page_title="目標登録">
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
