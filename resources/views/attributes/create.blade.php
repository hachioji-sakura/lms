@section('domain', 'attributes')
@section('domain_name', $select_key_name)
@section('title')
  @yield('domain_name')  @if(isset($_edit)) 編集 @else 登録 @endif
@endsection
@extends('dashboard.common')
@include('attributes.menu.page_sidemenu')

@section('contents')
<div class="card-header">
  <h3 class="card-title">@yield('title')</h3>
</div>
<div class="card-body">
  @if(isset($_edit))
  <form id="edit" method="POST" action="/@yield('domain')/{{$item['id']}}">
    @method('PUT')
  @else
  <form id="edit" method="POST" action="/@yield('domain')?key={{$select_key}}">
  @endif
  @csrf
    <div class="row">
      <div class="col-12">
        <div class="form-group">
          <label for="attribute_key" class="w-100">
            属性キー
          </label>
          {{$select_key_name}}({{$select_key}})
          <input type="hidden" id="attribute_key" name="attribute_key" value="{{$select_key}}">
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <div class="form-group">
          <label for="attribute_value" class="w-100">
            属性値
            <span class="right badge badge-danger ml-1">必須</span>
          </label>
          @if(isset($_edit))
          <input type="hidden" id="attribute_value_org" name="attribute_value_org" value="{{$item['attribute_value']}}">
          <input type="text" id="attribute_value" name="attribute_value" value="{{$item['attribute_value']}}" class="form-control" placeholder="{{$item['attribute_value']}}" required="true" >
          @else
          <input type="text" id="attribute_value" name="attribute_value" class="form-control" placeholder="例:1" required="true" >
          @endif
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <div class="form-group">
          <label for="attribute_name" class="w-100">
            属性名
            <span class="right badge badge-danger ml-1">必須</span>
          </label>
          @if(isset($_edit))
          <input type="text" id="attribute_name" name="attribute_name" value="{{$item['attribute_name']}}" class="form-control" placeholder="{{$item['attribute_name']}}" required="true" >
          @else
          <input type="text" id="attribute_name" name="attribute_name" class="form-control" placeholder="例：男性" required="true">
          @endif
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12 col-lg-6 col-md-6 mb-1">
          <button type="submit" class="btn btn-primary btn-block">
            @if(isset($_edit))
              更新する
            @else
              登録する
            @endif
          </button>
          @if(isset($error_message))
            <span class="invalid-feedback d-block ml-2 " role="alert">
                <strong>{{$error_message}}</strong>
            </span>
          @endif
      </div>
      <div class="col-12 col-lg-6 col-md-6 mb-1">
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
