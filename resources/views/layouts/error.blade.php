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
			<a href="/home"><button class="btn btn-primary"><i class="text-muted"></i>トップへ戻る</button></a>
		</div>
		<div class="help-block text-center mt-2">
			@yield('message')
		</div>
		<div class="lockscreen-footer text-center">
		</div>
	</div>

</body>
</html>
