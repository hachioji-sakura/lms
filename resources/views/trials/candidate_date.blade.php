@extends('layouts.simplepage')
@section('title', '体験授業候補日時再入力ページ')
@section('content')
<div id="">
    <div class="row">
      <div class="col-12">
        @component('trials.forms.trial_detail',['item'=>$item, 'attributes' => $attributes, 'user' => $user, 'domain' => $domain, 'domain_name' => $domain_name]) @endcomponent
      </div>
      @if($item->status=='reapply')
      <div class="col-12 mb-2">
        <a href="javascript:void(0);" id="trial_edit_link"
        page_title="申し込み内容編集" page_form="dialog"
        page_url="/parents/{{$item->id}}/trial_request?student_id={{$item->student_id}}&key={{$access_key}}"
        role="button" class="btn btn-primary btn-block">
          <i class="fa fa-edit mr-1"></i>
          体験授業希望日時の変更
        </a>
      </div>
      @endif
    </div>
  </form>
</div>

<script>
$(function(){
  $("#trial_edit_link").on("click", function(e){
    console.log($(this).attr('page_title'));
    base.showPage("dialog", "subDialog", $(this).attr("page_title"), $(this).attr("page_url"));
  });
});
</script>
@endsection
