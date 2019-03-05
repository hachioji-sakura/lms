@include('layouts.start')
@include('layouts.end')
@yield('start')

<body class="hold-transition lockscreen">
	<div class="lockscreen-wrapper">
		<div class="lockscreen-logo">
			@yield('error_title')
		</div>
		<div class="lockscreen-name">
			<p>@yield('error_description')</p>
			@yield('return_button')
		</div>
		<div class="help-block text-center mt-2">
			@yield('message')
		</div>
		<div class="lockscreen-footer text-center">
		</div>
	</div>

</body>
</html>
