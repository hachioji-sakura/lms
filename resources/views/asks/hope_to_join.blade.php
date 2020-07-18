@extends('layouts.loginbox')
@section('title')
  ご入会希望に関する連絡
@endsection
@section('title_header')@yield('title')@endsection
@section('content')
@if($item->status=='new')

<form method="POST" action="/asks/{{$item['id']}}/status_update/commit">
  @csrf
  <input type="text" name="dummy" style="display:none;" / >
  <div id="trials_entry" class="carousel slide" data-ride="carousel" data-interval="false">
  <div class="carousel-inner">
    <div class="carousel-item active">
      <div class="row">
        <div class="col-12 mb-1">
          <a href="javascript:void(0);" role="button" class="btn-next btn btn-primary btn-block float-left mr-1">
            次へ
            <i class="fa fa-arrow-circle-right ml-1"></i>
          </a>
        </div>
      </div>
    </div>
    <div class="carousel-item">
      <div class="row">
        <div class="col-12 mb-1">
          <a href="javascript:void(0);" role="button" class="btn-prev btn btn-secondary btn-block float-left mr-1">
            <i class="fa fa-arrow-circle-left mr-1"></i>
            戻る
          </a>
        </div>
        <div class="col-12 mb-1">
          <a href="javascript:void(0);" role="button" class="btn-next btn btn-primary btn-block float-left mr-1">
            次へ
            <i class="fa fa-arrow-circle-right ml-1"></i>
          </a>
        </div>
      </div>
    </div>
    <div class="carousel-item">
      <div class="row">
        <div class="col-12 mb-1">
          <a href="javascript:void(0);" role="button" class="btn-prev btn btn-secondary btn-block float-left mr-1">
            <i class="fa fa-arrow-circle-left mr-1"></i>
            戻る
          </a>
        </div>
        <div class="col-12 mb-1">
          <a href="javascript:void(0);" role="button" class="btn-next btn btn-primary btn-block float-left mr-1">
            次へ
            <i class="fa fa-arrow-circle-right ml-1"></i>
          </a>
        </div>
      </div>
    </div>
    <div class="carousel-item">
      <div class="row">
        <div class="col-12 mb-1">
          <a href="javascript:void(0);" role="button" class="btn-prev btn btn-secondary btn-block float-left mr-1">
            <i class="fa fa-arrow-circle-left mr-1"></i>
            戻る
          </a>
        </div>
        <div class="col-12 mb-1">
          <a href="javascript:void(0);" role="button" class="btn-next btn btn-primary btn-block float-left mr-1">
            次へ
            <i class="fa fa-arrow-circle-right ml-1"></i>
          </a>
        </div>
      </div>
    </div>
    <div class="carousel-item">
      <div class="row">
        <div class="col-12 mb-1">
          <a href="javascript:void(0);" role="button" class="btn-prev btn btn-secondary btn-block float-left mr-1">
            <i class="fa fa-arrow-circle-left mr-1"></i>
            戻る
          </a>
        </div>
        <div class="col-12 mb-1">
            <a href="javascript:void(0);" role="button" class="btn-next btn btn-primary btn-block float-left mr-1 btn-confirm">
              <i class="fa fa-file-alt mr-1"></i>
              内容確認
            </a>
        </div>
      </div>
    </div>
    <div class="carousel-item" id="confirm_form">
      <div class="row">
        <div class="col-12 mb-1">
          <a href="javascript:void(0);" role="button" class="btn-prev btn btn-secondary btn-block float-left mr-1">
            <i class="fa fa-arrow-circle-left mr-1"></i>
            戻る
          </a>
        </div>
        <div class="col-12 mb-1">
            <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="students_create">
                この内容でお申込み
                <i class="fa fa-caret-right ml-1"></i>
            </button>
        </div>
      </div>
    </div>
  </div>
</div>
</form>

@component('trials.forms.entry_page_script', ['_edit' => false]) @endcomponent

@elseif($item->status=='commit')
  <h4 class="bg-success p-3 text-sm">
    ご入会希望のご連絡を頂き、大変感謝致します。<br>
<br>
    改めて、通塾スケジュールについて、<br>
    ご連絡をいたしますので、お待ちください。
  </h4>
@elseif($item->status=='cancel')
<h4 class="bg-success p-3 text-sm">
  この度はご連絡いただき、誠にありがとうございました。<br>
  <br>
  ご入会キャンセルの件、承知しました。<br>
  <br>
  また、生徒様の学習方法・進学について、<br>
  お困りごとがありましたら、いつでも相談にのりますので、<br>
  ご気軽にご連絡ください。<br>
  <br>
  どうぞよろしくお願い申し上げます。
</h4>
@endif
@endsection
