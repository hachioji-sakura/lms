@include('layouts.start')
@include('layouts.end')
@include('layouts.modal')
@include('layouts.message')
@yield('start')

<body class="hold-transition login-page">
@component('components.action_message', [])
@endcomponent
<div class="float-right p-2">
	<a href="javascript:void(0);" onClick="setLocale('{{url()->current()}}', 'ja')">Japan</a> |
	<a href="javascript:void(0);" onClick="setLocale('{{url()->current()}}', 'en')">English</a>
</div>
<div class="login-box">
	<div class="login-logo p-4">
	<b>{{__('labels.system_name')}}</b>
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
function setLocale(url, locale){
	url = getLocaleUrl(url, locale);
	location.href = url;
}
function getLocaleUrl(url, locale){
	var param = util.convQueryStringToJson();
	if(!util.isEmpty(locale)) param['locale'] = locale;
	url = (url+"?").split("?");
	url = url[0];
	var is_first = true;
	for(var key in param){
		if(is_first == true){
			url+="?"+key+"="+param[key];
			is_first = false;
		}
		else {
			url+="&"+key+"="+param[key];
		}
	}
	return url;
}
$(function(){
  $("a[href]").each(function(){
    if($(this).attr('href').indexOf('void(0)')>0) return ;
    var url = getLocaleUrl($(this).attr('href'), "");
    $(this).attr("href", url);
  });

});
</script>
@yield('end')
