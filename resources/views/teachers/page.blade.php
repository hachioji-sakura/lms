@include('teachers.domain')
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
