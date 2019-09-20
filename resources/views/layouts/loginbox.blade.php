@include('layouts.start')
@include('layouts.end')
@include('layouts.modal')
@include('layouts.message')
@yield('start')

<body class="hold-transition login-page">
@component('components.action_message', [])
@endcomponent
<div class="float-right p-2">
	<a href="{{url()->current()}}" class="locale-ja">Japan</a> |
	<a href="{{url()->current()}}" class="locale-en">English</a>
</div>
<div class="login-box">
	<div class="login-logo">
	<a href="./"><b>{{__('labels.system_name')}}</b></a>
	</div>
	<div class="card">
		<div class="card-header">
			<h3 class="card-title">@yield('title_header')</h3>
		</div>
		<div class="card-body login-card-body">
      @yield('content')

			@yield('modal')

			@yield('message')

		</div>
  </div>
</div>
<script>
$(function(){
	var param = util.convQueryStringToJson();
  if(!util.isEmpty(param["locale"])){
    $("input[name='locale']").val(param["locale"]);
	}

  $("a[href]").each(function(){
    var url = $(this).attr('href');
		var _class = $(this).attr('class');
    url = (url+"?").split("?");
		if(_class=='locale-ja'){
			url = url[0]+"?locale=ja";
		}
		else if(_class=='locale-en'){
			url = url[0]+"?locale=en";
		}
		else if(!util.isEmpty(param["locale"])){
			url = url[0]+"?locale="+param["locale"];
		}
		else {
			url = url[0];
		}
		for(var key in param){
			if(key=="locale") continue;
			url+="&"+key+"="+param[key];
		}
    $(this).attr("href", url);
  });

});
</script>
@yield('end')
