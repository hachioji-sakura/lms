@section('title' , __('labels.signup'))

@include('layouts.start')
@include('layouts.end')
@include('layouts.modal')
@include('layouts.message')
@yield('start')
<body class="hold-transition lockscreen">
<!-- Automatic element centering -->
<div class="lockscreen-wrapper">
  <div class="lockscreen-logo">
    <b>{{__('labels.system_name')}}</b>
  </div>
  <!-- User name -->
  <div class="lockscreen-name">
    {{__('labels.signup')}}
  </div>
  <div class="help-block text-center">
    {!!nl2br(__('messages.info_signup'))!!}
  </div>

  <!-- START LOCK SCREEN ITEM -->
  <div id="_form" class="lockscreen-item">
    <form method="POST" action="/signup">
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
      <div class="input-group">
      	<div class="input-group-prepend">
      		<span class="input-group-text"><i class="fa fa-envelope"></i></span>
      	</div>
      	<input name="email" type="email" class="form-control" placeholder="Email" required="true" inputtype="email" >
        <div class="input-group-append">
          <button type="button" class="btn btn-submit btn-success"><i class="fa fa-arrow-right"></i></button>
        </div>
      </div>
    </form>
    <!-- /.lockscreen credentials -->

  </div>
  @if(!empty($result))
    @if($result=='already')
      <div class="alert alert-danger text-sm pr-2 text-sm mt-4">
        このメールアドレスは、すでに登録済みです
      </div>
    @elseif($result=='success')
    <div class="alert alert-success text-sm pr-2 text-sm mt-4">
      {{$email}}に本登録用のURLを送信しました
    </div>
    @else
    <div class="alert alert-danger text-sm pr-2 text-sm mt-4">
      {{$result}} / {{$res['description']}}
    </div>
    @endif
  @endif
  <!-- /.lockscreen-item -->
  <div class="text-center text-muted">
    {!!nl2br(__('messages.already_signup'))!!}
  </div>
  <div class="lockscreen-footer text-center">
    {{__('messages.copyright')}}
  </div>
</div>
<!-- /.center -->
<script>
$(function(){
  base.pageSettinged("_form", null);
  //submit
  $("button.btn-submit").on('click', function(e){
    e.preventDefault();
    if(front.validateFormValue('_form')){
      $("form").submit();
    }
  });
});
</script>

@yield('end')
