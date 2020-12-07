@extends('layouts.simplepage')
@section('title')
  @if($item->type == 'agreement')
  入会のご案内
  @elseif($item->type == 'agreement_confirm')
  契約変更のご案内
  @endif
@endsection
@section('title_header')
<ol class="step">
  <li id="step_input" class="is-current">@yield('title')</li>
</ol>
@endsection
@section('content')
@if($item->status=='new' )
<div id="admission_mail">
  <form method="POST" action="/asks/{{$item['id']}}/status_update/commit">
    @method('PUT')
    <input type="text" name="dummy" style="display:none;" / >
    <input type="hidden" name="key" value="{{$access_key}}" />
    @component('trials.forms.admission_schedule', [ 'attributes' => $attributes, 'prefix'=>'', 'item' => $agreement, 'agreement' => $agreement, 'domain' => $domain, 'input'=>false, 'active_tab' => 1]) @endcomponent
    @csrf
	<input type="text" name="dummy" style="display:none;" / >
    <section class="content-header">
    	<div class="container-fluid">
        @if($item->type == 'agreement')
        <div class="row">
          <div class="col-12 mt-2 mb-1">
            <div class="form-group">
              <label for="remark" class="w-100">
                入会に関する重要事項
              </label>
              <textarea type="text" id="body" name="remark" class="form-control bg-light" placeholder="" readonly>
@component('asks.forms.agreement_policy', []) @endcomponent
1, 2, 3, 4, 5をご了承していただけますならば、
下記ボタンよりご連絡いただけますと幸いです。
              </textarea>
            </div>
          </div>
        </div>
        @endif
        <div class="row">
          <div class="col-12 mt-2 mb-1">
            <div class="form-group">
              <input class="form-check-input icheck flat-green" type="checkbox" id="important_check" name="important_check" value="1" required="true" onChange="important_checked()">
              <label class="form-check-label" for="important_check">
                {{__('labels.agreement_check')}}
              </label>
            </div>
          </div>
          <div class="col-12 mb-1" id="commit_form">
            <form method="POST" action="/asks/{{$item['id']}}/status_update/commit">
              @csrf
              <input type="text" name="dummy" style="display:none;" / >
              @method('PUT')
              <div class="col-12 mb-1">
                <button type="button" class="btn btn-submit btn-success btn-block"  accesskey="commit_form" disabled="disabled">
                  <i class="fa fa-check mr-1"></i>
                  {{__('labels.send_button')}}
                </button>
              </div>
            </form>
          </div>
          <script>
          $(function(){
            base.pageSettinged("commit_form", null);
            //submit
            $("#commit_form button.btn-submit").on('click', function(e){
              e.preventDefault();
              if(front.validateFormValue('commit_form')){
                $(this).prop("disabled",true);
                $("#commit_form form").submit();
              }
            });
            $("body").on("ifChecked", "#important_check", function(e){
              $("#commit_form button.btn-submit").prop("disabled",false);
            });
            $("body").on("ifUnchecked", "#important_check", function(e){
              $("#commit_form button.btn-submit").prop("disabled",true);
            });
          });
          </script>
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
function important_checked(){
  var ret = $('input[name="important_check"]').prop('checked');
  $("button.btn-submit").prop("disabled",!ret);
}
</script>
@elseif($item->status=='commit')
  @if($agreement->student_parent->user->status==0)
  <h4 class="bg-success p-3 text-sm">
    @if($item->type == 'agreement')
    ご入会のご連絡を頂き、大変感謝致します。<br>
    @elseif($item->type == 'agreement_confirm')
    ご契約内容は下記の通りとなります。<br>
    @endif
  </h4>
  @else
  <h4 class="bg-success p-3 text-sm">
    ご入会のご連絡を頂き、大変感謝致します。<br>
    <br>
    大変お手数ですが、システムへのユーザー登録をしていただけますと幸いです。  <br>
    <br>
  </h4>
    <div class="col-12 mb-1" id="commit_form">
      <a role="button" class="btn btn-submit btn-primary btn-block"  href="{{config('app.url')}}/register?key={{$access_key}}">
        <i class="fa fa-sign-in-alt mr-1"></i>
        ユーザー登録画面
      </a>
    </div>
  @endif
  @component('students.forms.agreement', ['item' => $student, 'fields' => $fields, 'domain' => $domain, 'user'=>$user, 'agreement' => $student->enable_agreements_by_type('normal')->first()]) @endcomponent
  <div class="row">
    <div class="col-12 mb-1">
      @if(isset($user) && $user->id == $agreement->student_parent_id)
      <a class="btn btn-block btn-secondary" href="/" >
        <i class="fa fa-arrow-right mr-1"></i>
        {{__('labels.top')}}へ
      </a>
      @else
      <a href="/login" class="btn btn-secondary btn-block">
        <i class="fa fa-sign-in-alt mr-1"></i>
        {{__('labels.to_login')}}
      </a>
      @endif
    </div>
  </div>
@elseif($item->status=='cancel')
<h4 class="bg-success p-3 text-sm">
  この度はご連絡いただき、誠にありがとうございました。

  ご入会キャンセルの件、承知しました。

  また、生徒様の学習方法・進学について、
  お困りごとがありましたら、いつでも相談にのりますので、
  ご気軽にご連絡ください。

  どうぞよろしくお願い申し上げます。
</h4>
@endif
@endsection
