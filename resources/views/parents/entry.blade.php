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
        <label for="name_first">
          {{__('labels.name_first')}}
          <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
        </label>
        <input type="text" id="name_first" name="name_first" class="form-control" placeholder="例：太郎" required="true" inputtype="zenkaku" >
      </div>
      <div class="input-group">
      	<div class="input-group-prepend">
      		<span class="input-group-text"><i class="fa fa-envelope"></i></span>
      	</div>
      	<input type="email" class="form-control" placeholder="Email" required="true" inputtype="email" query_check="users/email" query_check_error="{{__('messages.message_already_email')}}">

        <div class="input-group-append">
          <button type="button" class="btn btn-submit btn-success"><i class="fa fa-arrow-right"></i></button>
        </div>
      </div>
    </form>
    <!-- /.lockscreen credentials -->

  </div>
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
