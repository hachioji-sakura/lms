@extends('layouts.simplepage')
@section('title')
  入会のご案内
@endsection
@section('title_header')
<ol class="step">
  <li id="step_input" class="is-current">@yield('title')</li>
</ol>
@endsection
@section('content')
@if($item->status=='new')
<div id="admission_mail">
  <form method="POST" action="/asks/{{$item['id']}}/status_update/commit">
    <input type="hidden" name="key" value="{{$access_key}}" />
    @component('trials.forms.admission_schedule', [ 'attributes' => $attributes, 'prefix'=>'', 'item' => $trial, 'domain' => $domain, 'input'=>false, 'active_tab' => 1]) @endcomponent
    @csrf
	<input type="text" name="dummy" style="display:none;" / >
    <section class="content-header">
    	<div class="container-fluid">
        <div class="row">
          <div class="col-12 mt-2 mb-1">
            <div class="form-group">
              <label for="remark" class="w-100">
                入会に関する重要事項
              </label>
              <textarea type="text" id="body" name="remark" class="form-control bg-light" placeholder="" readonly>
この度は入会を希望して頂き、誠に感謝しております。
以下の４点をご承諾いただけますと、御入会になります。
1. 入会規約
2. 以下に記載した受講料等
3. 振替授業をされる際、本来の授業日における受講料は発生致しますが、2ヶ月以内であれば、無料で振替授業を行わせていただきます。
4. 受講料支払いは当月分受講料の請求を翌月10日頃に行い、支払い期限が翌月20日までとなりますが、翌月月末までにお支払いいただけない場合は、事務手数料1,000円（+ 消費税）がかかります。
支払い例）1月分受講料&rarr;2月10日頃明細メール到着&rarr;2月20日までにご入金
1, 2, 3, 4をご了承していただけますならば、
「了承します」と返信していただけますと幸いです。
              </textarea>
            </div>
          </div>
        </div>
    		<div class="row">
          <div class="col-12 mt-2 mb-1">
            <div class="form-group">
              <input class="form-check-input icheck flat-green" type="checkbox" id="important_check" name="important_check" value="1" required="true" >
              <label class="form-check-label" for="important_check">
                {{__('labels.important_check')}}
              </label>
            </div>
          </div>
          <div class="col-12 mb-1" id="commit_form">
            <form method="POST" action="/asks/{{$item['id']}}/status_update/commit">
              @csrf
              <input type="text" name="dummy" style="display:none;" / >
              @method('PUT')
              <button type="button" class="btn btn-submit btn-success btn-block"  accesskey="commit_form">
                <i class="fa fa-check mr-1"></i>
                上記の内容について了承しました
              </button>
            </form>
          </div>
          <script>
          $(function(){
            base.pageSettinged("commit_form", null);
            //submit
            $("#commit_form button.btn-submit").on('click', function(e){
              e.preventDefault();
              if(front.validateFormValue('commit_form')){
                $("#commit_form form").submit();
              }
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
</script>
@elseif($item->status=='commit')
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
