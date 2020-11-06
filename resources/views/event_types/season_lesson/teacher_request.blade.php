@extends('layouts.simplepage')
@section('title')
  期間講習勤務設定（講師）
@endsection
@section('title_header')
<ol class="step">
  <li id="step_input" class="bg-info">@yield('title')</li>
</ol>
@endsection
@include('event_types.season_lesson.create_form')


@section('content')
<div class="direct-chat-msg">
  @if(!empty($result))
    <h4 class="bg-success p-3 text-sm">
      @if($result==="success")
      {!!nl2br(__('messages.trial_entry1'))!!}
  <br><br>
      {!!nl2br(__('messages.trial_entry2'))!!}
      @endif
    </h4>
  @else
  <form method="POST"  action="/events/100/answer">
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    <input type="hidden" name="lesson[]" value="1" />

    <div id="season_lesson_entry" class="carousel slide" data-ride="carousel" data-interval="false">
      <div class="carousel-inner">

        <div class="carousel-item active">
          <div class="row">
            <div class="col-12 bg-info p-2 pl-4 mb-4">
              <i class="fa fa-file-invoice mr-1"></i>
              勤務希望教室
            </div>
            @component('students.forms.lesson_place', ['_edit'=>$_edit, 'event'=>$event, 'attributes' => $attributes]) @endcomponent
          </div>
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
          <div class="col-12 bg-info p-2 pl-4 mb-4">
            <i class="fa fa-file-invoice mr-1"></i>
            勤務可能日時
          </div>
          @component('event_types.season_lesson.hope_datetime', ['_edit'=>$_edit, 'start_date'=>'2020-07-23', 'end_date' => '2020-08-31', 'event'=>$event,'attributes' => $attributes]) @endcomponent
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
            <div class="col-6 p-3 font-weight-bold" >勤務希望教室</div>
            <div class="col-6 p-3"><span id="lesson_place_name"></span></div>
            <div class="col-12 p-3 font-weight-bold">
              ご希望の日時
            </div>
            <div class="col-12">
              <div class="form-group">
                <table class="table table-striped">
                <tr class="bg-gray">
                  <th class="p-1 text-center ">日
                  </th>
                  <th class="p-1 text-center ">時間帯
                  </th>
                </tr>
                <tbody id="hope_datetime_list">
                </tbody>
                </table>
              </div>
            </div>
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-prev btn btn-secondary btn-block float-left mr-1">
                <i class="fa fa-arrow-circle-left mr-1"></i>
                戻る
              </a>
            </div>
            <div class="col-12 mb-1">
                <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="students_create">
                  <i class="fa fa-check mr-1"></i>
                    保存
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
