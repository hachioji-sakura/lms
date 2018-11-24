@section('domain', 'attributes')
@section('domain_name', '属性')
@section('title')
  @yield('domain_name')登録
@endsection
@extends('dashboard.common')
@include('dashboard.menu.page_sidemenu')

@section('contents')
<div class="card-header">
  <h3 class="card-title">@yield('title')</h3>
</div>
<div class="card-body">
  <form id="edit" method="POST" action="/@yield('domain')">
  @csrf
    <div class="row">
      <div class="col-12">
        <div class="form-group">
          <label for="attribute_key">
            属性キー
            <span class="right badge badge-danger ml-1">必須</span>
          </label>
          <input type="text" id="attribute_key" name="attribute_key" class="form-control" placeholder="例：gender" required="true" inputtype="hankaku">
        </div>
      </div>
      <div class="col-12">
        <div class="form-group">
          <label for="attribute_value">
            属性値
            <span class="right badge badge-danger ml-1">必須</span>
          </label>
          <input type="text" id="attribute_value" name="attribute_value" class="form-control" placeholder="例:1" required="true" >
      </div>
      <div class="col-12">
        <div class="form-group">
          <label for="attribute_name">
            属性名
            <span class="right badge badge-danger ml-1">必須</span>
          </label>
          <input type="text" id="attribute_name" name="attribute_name" class="form-control" placeholder="例：男性" required="true">
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12 col-lg-6 col-md-6">
          <button type="submit" class="btn btn-primary btn-block">
              登録する
          </button>
          @if(isset($error_message))
            <span class="invalid-feedback d-block ml-2 " role="alert">
                <strong>{{$error_message}}</strong>
            </span>
          @endif
      </div>
      <div class="col-12 col-lg-6 col-md-6">
          <button type="button" class="btn btn-secondary btn-block" accesskey="cancel" onClick="history.back();">
              キャンセル
          </button>
      </div>
    </div>
  </form>
</div>
<script>
$(function(){
  @if(env('APP_DEBUG'))
  var n1 = (Math.random()*100|0);
  var data = {
    "attribute_key" : "test_attribute",
    "attribute_value" : n1,
    "attribute_name" : "属性"+n1
  };
  base.pageSettinged("edit", data);
  @else
  base.pageSettinged("edit", null);
  @endif
	$(".btn[type=submit]").on("click", function(){
		if(!front.validateFormValue("edit")) return false;
    $("#edit").submit();
	});
})
</script>
@endsection
