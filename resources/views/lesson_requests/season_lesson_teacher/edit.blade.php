@include('season_lesson.create_form')
<div class="direct-chat-msg">
  <form method="POST"  action="/lesson_requests/{{$item->id}}">
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    <input type="hidden" name="lesson[]" value="1" />
    <input type="hidden" name="type" value="season_lesson" />
    <input type="hidden" name="event_user_id" value="{{$event_user_id}}" />
    <input type="hidden" name="access_key" value="{{$access_key}}" />
    <input type="hidden" name="domain" value="{{$domain}}" />
    <input type="hidden" name="hope_timezone" value="order" />
    @method('PUT')
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
          @if($_edit==false)
          <div class="row">
            @component($domain.'.season_lesson.hope_timezone', ['_edit'=>$_edit, 'attributes' => $attributes, 'title' => '勤務可能時間帯']) @endcomponent

          </div>
          @endif
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
          @component($domain.'.season_lesson.hope_datetime', ['_edit'=>$_edit, 'event_dates' => $event_dates ,'attributes' => $attributes, 'item' => $item]) @endcomponent
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
