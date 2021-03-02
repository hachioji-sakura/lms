@extends('layouts.simplepage')
@section('title', '期間講習申込ページ')

@if($is_already_data == false)
  @section('title_header')
  <ol class="step">
    <li id="step_input" class="is-current">ご入力</li>
    <li id="step_confirm">ご確認</li>
    <li id="step_complete">完了</li>
  </ol>
  @endsection
  @include('lesson_requests.season_lesson.create_form')
@else
  @section('title_header')
  <ol class="step">
    <li id="step_input">ご入力</li>
    <li id="step_confirm">ご確認</li>
    <li id="step_complete" class="is-current">完了</li>
  </ol>
  @endsection
@endif

@section('content')
<div class="direct-chat-msg">
  @if($is_already_data == true)
    <h4 class="bg-success p-3 text-sm">
      授業をお申し込みいただきまして、
      誠にありがとうございます。
      <br><br>
      ２営業日以内に、当塾よりご連絡いたしますので、
      何卒宜しくお願い致します。
    </h4>
    <div class="row">
      <div class="col-12 mb-1">
        <a href="javascript:void(0);" page_form="dialog" page_title="お申し込み内容の確認" page_url="/lesson_requests/{{$lesson_request->id}}?event_user_id={{$event_user_id}}&access_key={{$access_key}}" role="button" class="btn-next btn btn-secondary btn-block float-left mr-1">
          <i class="fa fa-file ml-1"></i>
          お申し込み内容の確認
        </a>
      </div>
      @if($lesson_request->status=='new')
      <div class="col-12 mb-1">
        <a href="javascript:void(0);" page_form="dialog" page_title="お申し込み内容の変更" page_url="/lesson_requests/{{$lesson_request->id}}/edit?event_user_id={{$event_user_id}}&access_key={{$access_key}}" role="button" class="btn-next btn btn-primary btn-block float-left mr-1">
          <i class="fa fa-edit ml-1"></i>
          お申し込み内容の変更
        </a>
      </div>
      @endif
    </div>
  @else
  <form method="POST"  action="/lesson_requests">
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    <input type="hidden" name="lesson[]" value="1" />
    <input type="hidden" class="grade" name="grade" value="{{$item->grade(true)}}" />
    <input type="hidden" name="grade_name" value="{{$item->grade_name}}" />
    <input type="hidden" name="type" value="season_lesson" />
    <input type="hidden" name="event_user_id" value="{{$event_user_id}}" />
    <input type="hidden" name="access_key" value="{{$access_key}}" />
    <input type="hidden" name="domain" value="{{$domain}}" />
    <input type="hidden" name="domain_item_id" value="{{$item->id}}" />
    <input type="hidden" name="send_mail" value="true" />

    <div id="season_lesson_entry" class="carousel slide" data-ride="carousel" data-interval="false">
      <div class="carousel-inner">
        <div class="carousel-item active">
          @yield('first_form')
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
          @yield('hope_datetime')
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
          @yield('subject_form')
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
          @yield('survey_form')
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
          @component($domain.'.season_lesson.confirm_form', ['attributes' => $attributes, 'is_trial' => true]) @endcomponent
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
</div>
@component('trials.forms.entry_page_script', ['_edit' => $_edit]) @endcomponent
@endif
@endsection
