@section('title')
  {{$domain_name}}ダッシュボード
@endsection
@extends('dashboard.common')

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
        @component('components.profile', ['item' => $item, 'user' => $user, 'use_icons' => $use_icons, 'domain' => $domain, 'domain_name' => $domain_name])
            @slot('courtesy')
            @endslot
            @slot('alias')
            @endslot
        @endcomponent
			</div>
			<div class="col-md-8">
				@yield('comments')
			</div>
		</div>
	</div>
</section>
{{-- 担当生徒一覧　（まだ対応しない）
<section class="content mb-2">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title" id="charge_students">
            <i class="fa fa-users mr-1"></i>
            担当生徒
          </h3>
          <div class="card-tools">
            <div class="input-group input-group-sm" style="width: 150px;">
              <input type="text" name="table_search" class="form-control float-right" placeholder="Search">
              <div class="input-group-append">
                <button type="submit" class="btn btn-default">
                  <i class="fa fa-search"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body table-responsive p-0">
          <table class="table table-hover">
            <tbody>
              <tr>
                <th>ID</th>
                <th>名前</th>
                <th>次回レッスン</th>
                <th>-</th>
              </tr>
              <tr>
                <td>183</td>
                <td>
                  <ruby style="ruby-overhang: none">
                    <rb>山田　太郎</rb>
                    <rt>ヤマダ　タロウ</rt>
                  </ruby>
                </td>
                <td>
                  <i class="fa fa-calendar mr-1"></i>
                  2000/11/11
                </td>
                <td>
                  <button type="button" class="btn btn-danger btn-sm float-left">
                    <i class="fa fa-minus-circle mr-2"></i>外す
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <!-- /.card-body -->
        <div class="card-footer clearfix">
          <button type="button" class="btn btn-info btn-sm float-left">
            <i class="fa fa-plus mr-2"></i>追加
          </button>
        </div>
      </div>
      <!-- /.card -->
    </div>
  </div>
</section>
--}

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
          <a class="nav-link" href="javascript:void(0);"  page_form="footer_form" page_url="/comments/create?_page_origin={{$domain}}_{{$item->id}}&teacher_id={{$item->id}}" page_title="目標登録">
            <i class="fa fa-comment-dots nav-icon"></i>コメント登録
          </a>
        </li>
        {{--
        <li class="nav-item">
          <a class="nav-link" href="javascript:void(0);"  page_form="footer_form" page_url="/milestones/create?_page_origin={{$domain}}_{{$item->id}}&teacher_id={{$item->id}}" page_title="目標登録">
            <i class="fa fa-flag nav-icon"></i>目標登録
          </a>
        </li>
        --}}
      </ul>
    </li>
</ul>
@endsection

@section('page_footer')
<dt>
  <a class="btn btn-app" href="javascript:void(0);" page_form="footer_form" page_url="/comments/create?_page_origin={{$domain}}_{{$item->id}}&teacher_id={{$item->id}}" page_title="コメント登録">
    <i class="fa fa-comment-dots"></i>コメント登録
  </a>
</dt>
{{-- まだ対応しない
  <dt>
    <a class="btn btn-app" href="javascript:void(0);"  page_form="footer_form" page_url="/milestones/create?_page_origin={{$domain}}_{{$item->id}}&teacher_id={{$item->id}}" page_title="目標登録">
      <i class="fa fa-flag"></i>目標登録
    </a>
  </dt>
--}}
@endsection
