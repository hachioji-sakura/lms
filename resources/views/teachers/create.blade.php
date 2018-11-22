@include('teachers.domain')
@extends('dashboard.create')

@section('scripts')
<script>
$(function(){
  @if((env('APP_DEBUG')))
  var data = {
    "name" : "鈴木　一郎",
    "kana" : "すずき　いちろう",
    "email" : "suzuki"+((Math.random()*1000)|0)+"@gmail.com",
    "password-confirm" : "hogehoge",
    "password" : "hogehoge"
  };
  base.pageSettinged("edit", data);
  @else
  base.pageSettinged("edit", null);
  @endif
	$(".btn[type=submit]").on("click", function(){
		console.log("btn.submit");
		if(!front.validateFormValue("edit")) return false;
    $("#edit").submit();
	});
})
</script>

@endsection
