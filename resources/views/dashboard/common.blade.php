@include('layouts.start')
@include('layouts.end')
@include('layouts.modal')
@include('layouts.message')
@include('dashboard.menu.navbar')
@include('dashboard.menu.sidemenu')
@include('dashboard.menu.footer')

@yield('start')
<body class="hold-transition sidebar-mini">
<div class="wrapper">
	@yield('navbar')
	@yield('sidemenu')
  <div class="content-wrapper">
		@if(session('error_message'))
		<div class="row">
			<div class="col-12">
			  <div class="card bg-danger-gradient">
			    <div class="card-header">
			      <h3 class="card-title">{{session('error_message')}}</h3>
			      <div class="card-tools">
			        <button type="button" class="btn btn-tool" data-widget="remove">
			          <i class="fa fa-times"></i>
			        </button>
			      </div>
			    </div>
					@if(env('APP_DEBUG') && session('error_message_description'))
			    <div class="card-body">{{session('error_message_description')}}</div>
					@endif
			  </div>
			</div>
		</div>
		@endif
		@if(session('success_message'))
		<div class="row">
			<div class="col-12">
			  <div class="card bg-success-gradient">
			    <div class="card-header">
			      <h3 class="card-title">{{session('success_message')}}</h3>
			      <div class="card-tools">
			        <button type="button" class="btn btn-tool" data-widget="remove">
			          <i class="fa fa-times"></i>
			        </button>
			      </div>
			    </div>
					@if(env('APP_DEBUG') && session('success_message_description'))
			    <div class="card-body">{{session('success_message_description')}}</div>
					@endif
			  </div>
			</div>
		</div>
		@endif
		@yield('contents')

		@yield('modal')

		@yield('message')

  </div>
  <footer class="main-footer">
		@yield('footer')
  </footer>

</div>

@yield('end')
