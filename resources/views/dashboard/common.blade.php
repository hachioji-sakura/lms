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
		<section id="main" class="content">
			@component('components.action_message', [])
			@endcomponent
		  <div class="container-fluid">
		    <div class="row p-1">
					<div class="col-12">
					  <div class="card">
							@yield('contents')
						</div>
					</div>
				</div>
			</div>
			</section>

		@yield('modal')

		@yield('message')

  </div>
  <footer class="main-footer">
		@yield('footer')
  </footer>

</div>

@yield('end')
