@extends('layouts.simplepage')
@section('title')
  入会案内メール送信
@endsection
@section('title_header')
<ol class="step">
  <li id="step_input" class="is-current">@yield('title')</li>
</ol>
@endsection
@section('content')
<div id="admission_mail">
  <form method="POST"  action="/{{$domain}}/{{$item->id}}">
    @component('trials.forms.admission_schedule', [ 'attributes' => $attributes, 'prefix'=>'', 'item' => $item->get_target_model_data(), 'domain' => $domain, 'input'=>false]) @endcomponent
    @csrf
    <section class="content-header">
    	<div class="container-fluid">
    		<div class="row">
    			<div class="col-6 mb-1">
    				<button type="button" class="btn btn-submit btn-primary btn-block" accesskey="admission_mail" confirm="入会案内メールを送信しますか？">
    					<i class="fa fa-envelope mr-1"></i>
    					入会案内メールを送信する
    				</button>
    			</div>
    			<div class="col-6 mb-1">
    				<a href="/{{$domain}}/{{$item->id}}" role="button" class="btn btn-secondary btn-block float-left mr-1">
    					<i class="fa fa-arrow-circle-left mr-1"></i>
    					{{__('labels.cancel_button')}}
    				</a>
    			</div>
    		</div>
    	</div>
    </section>
  </form>
</div>
<script>
$(function(){
  base.pageSettinged("admission_mail", null);
  $("button.btn-submit").on('click', function(e){
    e.preventDefault();
    var _confirm = $(this).attr("confirm");
    if(!util.isEmpty(_confirm)){
      if(!confirm(_confirm)) return false;
    }
    if(front.validateFormValue('admission_mail')){
      $("form").submit();
    }
  });
});
</script>
@endsection
