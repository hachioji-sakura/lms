@include('layouts.start')
@include('layouts.end')
@yield('start')

<body class="hold-transition login-page">
@component('components.action_message', [])
@endcomponent
<div class="login-box">
	<div class="login-logo">
	<a href="./"><b>{{config('app.name')}}</b></a>
	</div>
	<div class="card">
		<div class="card-header">
			<h3 class="card-title">@yield('title_header')</h3>
		</div>
		<div class="card-body login-card-body">
      @yield('content')
		</div>
  </div>
</div>

@yield('end')
