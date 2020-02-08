@extends('layouts.simplepage')
@section('title', '体験授業候補日時再入力ページ')
@section('content')
<div class="direct-chat-msg">
  <form method="POST"  action="/entry">
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    <div id="candidate_date_form" >
      <div class="row">
        <div class="col-12 bg-info p-2 pl-4 mb-4">
          <i class="fa fa-file-invoice mr-1"></i>
          体験授業ご希望の日時を再度おしらせください。
        </div>
        @component('trials.forms.trial_date', ['_edit'=>false, 'item'=>null,'attributes' => $attributes]) @endcomponent
      </div>
      <div class="row">
        <div class="col-12 mb-1">
          <a role="button" class="btn btn-submit btn-success btn-block" href="javascript:void(0);" page_url="/trials/{{$item->id}}/edit" page_title="お申込み内容の変更" page_form="dialog" >
            <i class="fa fa-edit mr-1"></i>
            お申込み内容の編集
          </a>
          <button type="button" class="btn btn-submit btn-primary btn-block">
              追加の候補日を連絡する
              <i class="fa fa-caret-right ml-1"></i>
          </button>
        </div>
      </div>
    </div>
  </form>
</div>
<script>

$(function(){
  base.pageSettinged("candidate_date_form", null);
  //submit
  $("button.btn-submit").on('click', function(e){
    e.preventDefault();
    if(front.validateFormValue('candidate_date_form')){
      $("form").submit();
    }
  });

});
</script>
@endsection
