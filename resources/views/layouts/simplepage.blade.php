@include('layouts.start')
@include('layouts.end')
@include('layouts.modal')
@include('layouts.message')
@yield('start')

<body class="hold-transition login-page">
<div class="">
	<div class="card m-2">
		<div class="card-header">
			<h3 class="card-title">@yield('title')</h3>
		</div>
		<div class="card-body login-card-body">
      @yield('content')
			@component('components.action_message', [])
			@endcomponent

		</div>
  </div>
	@yield('modal')

	@yield('message')

</div>

@yield('end')
