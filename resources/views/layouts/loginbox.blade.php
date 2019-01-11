@include('layouts.start')
@include('layouts.end')
@yield('start')

<body class="hold-transition login-page">
<div class="login-box">
	<div class="login-logo">
	<a href="./"><b>学習管理システム</b></a>
	</div>
	<div class="card">
		<div class="card-header">
			<h3 class="card-title">@yield('title')</h3>
		</div>
		<div class="card-body login-card-body">
      @yield('content')
			@component('components.action_message', [])
			@endcomponent

		</div>
  </div>
</div>

@yield('end')
