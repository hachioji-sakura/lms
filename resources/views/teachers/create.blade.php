@include($domain.'.entry_form')
<div id="{{$domain}}_entry">
  <form method="POST"  action="/{{$domain}}/entry">
    
@csrf
		<input type="text" name="dummy" style="display:none;" / >
    @yield('entry_form')
    <div class="row">
      <div class="col-12 col-lg-6 col-md-6 mb-1">
        <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="{{$domain}}_create">
          <i class="fa fa-envelope mr-1"></i>登録メールを送る
        </button>
      </div>
      <div class="col-12 col-lg-6 col-md-6 mb-1">
        <a href="javascript:void(0);" data-dismiss="modal" role="button" class="btn btn-secondary btn-block float-left mr-1">
          <i class="fa fa-times-circle mr-1"></i>
          キャンセル
        </a>
      </div>
    </div>
  </form>
</div>
