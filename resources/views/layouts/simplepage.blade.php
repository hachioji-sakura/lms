@include('layouts.start')
@include('layouts.end')
@include('layouts.modal')
@include('layouts.message')
@yield('start')

<body class="hold-transition login-page">
	<div class="card-header p-0 w-100" style="position:fixed; z-index:1;">
		@yield('title_header')
	</div>
	<div class="card-body login-card-body" style="margin-top:44px">
		@yield('content')
	</div>
	@yield('modal')
	@yield('message')


@yield('end')
