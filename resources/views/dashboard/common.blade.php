@include('layouts.start')
@include('layouts.end')
@include('layouts.modal')
@include('layouts.message')

@yield('start')
<body class="hold-transition sidebar-mini">
<div class="wrapper">
	@component('components.menu.navbar', ['user' => $user, 'domain' => $domain, 'domain_name' => $domain_name])
	@endcomponent
	@component('components.menu.sidemenu', ['user' => $user, 'domain' => $domain, 'domain_name' => $domain_name])
		@slot('setting')
			@yield('page_sidemenu')
		@endslot
	@endcomponent
  <div class="content-wrapper">
		<section id="main" class="content">
			@component('components.action_message', [])
			@endcomponent
		  <div class="container-fluid pt-1">
				@yield('contents')
			{{--
		    <div class="row p-1">
					<div class="col-12">
					  <div class="card">
							@yield('contents')
						</div>
					</div>
				</div>
			--}}
			</div>
			</section>

		@yield('modal')

		@yield('message')

  </div>
  <footer class="main-footer">
		@component('components.menu.footer', ['user' => $user, 'domain' => $domain, 'domain_name' => $domain_name])
			@slot('setting')
				@yield('page_footer')
			@endslot
		@endcomponent
  </footer>

</div>

@yield('end')
