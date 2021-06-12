@extends('layouts.simplepage')
@section('title')
  期間講習勤務設定（講師）
@endsection
@section('title_header')
<ol class="step">
  <li id="step_input" class="bg-info">@yield('title')</li>
</ol>
@endsection


@section('content')
<div class="direct-chat-msg">
  <div class="row">
    <div class="col-6 p-3 font-weight-bold" >イベント名</div>
    <div class="col-6 p-3">{{$event->title}}</div>
    <div class="col-6 p-3 font-weight-bold" >実施期間</div>
    <div class="col-6 p-3">{{$event->event_term}}</div>
    <div class="col-6 p-3 font-weight-bold" >回答期間</div>
    <div class="col-6 p-3">{{$event->response_term}}</div>
  </div>
  @if($is_already_data == true)

    <div class="row">
      <div class="col-12 mb-1">
        <a href="javascript:void(0);" page_form="dialog" page_title="講習勤務日時の確認" page_url="/lesson_requests/{{$lesson_request->id}}?event_user_id={{$event_user_id}}&access_key={{$access_key}}" role="button" class="btn-next btn btn-secondary btn-block float-left mr-1">
          <i class="fa fa-file ml-1"></i>
          講習勤務日時の確認
        </a>
      </div>
      @if($lesson_request->status=='new')
      <div class="col-12 mb-1">
        <a href="javascript:void(0);" page_form="dialog" page_title="講習勤務日時の変更" page_url="/lesson_requests/{{$lesson_request->id}}/edit?event_user_id={{$event_user_id}}&access_key={{$access_key}}" role="button" class="btn-next btn btn-primary btn-block float-left mr-1">
          <i class="fa fa-edit ml-1"></i>
          講習勤務日時の変更
        </a>
      </div>
      @endif
    </div>
  @else
  <form method="POST"  action="/lesson_requests">
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    <input type="hidden" name="lesson[]" value="1" />
    <input type="hidden" name="event_user_id" value="{{$event_user_id}}" />
    <input type="hidden" name="access_key" value="{{$access_key}}" />
    <input type="hidden" name="type" value="season_lesson_teacher" />
    <input type="hidden" name="domain" value="{{$domain}}" />
    <input type="hidden" name="domain_item_id" value="{{$item->id}}" />
    <input type="hidden" name="hope_timezone" value="order" />

    <div id="season_lesson_entry" class="carousel slide" data-ride="carousel" data-interval="false">
      <div class="carousel-inner">

        <div class="carousel-item active">
          <div class="row">
            <div class="col-12 bg-info p-2 pl-4 mb-4">
              <i class="fa fa-file-invoice mr-1"></i>
              勤務設定
            </div>
            @component('students.forms.lesson_place', ['_edit'=>true, 'event'=>$event, 'attributes' => $attributes, 'title' => '勤務可能校舎', 'item'=>$item]) @endcomponent
          </div>
          {{--
          <div class="row">
            @component('lesson_requests.season_lesson.hope_timezone', ['_edit'=>$_edit, 'attributes' => $attributes, 'title' => '勤務可能時間帯', 'item' => $item]) @endcomponent
          </div>
          --}}
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
          @component('lesson_requests.season_lesson.hope_datetime', ['_edit'=>$_edit, 'event_dates' => $event_dates ,'attributes' => $attributes, 'item' => $item]) @endcomponent
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
