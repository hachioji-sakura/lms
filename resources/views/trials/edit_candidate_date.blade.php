@include('trials.create_form')
<div class="direct-chat-msg">
  <form method="POST"  action="/trials/{{$item->id}}/candidate_date?key={{$access_key}}">
    @method('PUT')
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    <div class="col-12">
      授業を受ける生徒様：{{$student1->name()}} 様 / {{$student1->grade()}}
    </div>
    <input type="hidden" name="grade_name" value="{{$student1->grade()}}">
    <input type="hidden" class="grade" name="grade" value="{{$student1->tag_value('grade')}}">
    <input type="hidden" name="student_id" value="{{$student1->id}}">
    <input type="hidden" name="student_parent_id" value="{{$student_parent_id}}">
    <div id="trials_entry" class="carousel slide" data-ride="carousel" data-interval="false">
      <div class="carousel-inner">
        <div class="carousel-item active">
          @yield('candidate_form')
          <div class="row">
            <div class="col-12 mb-1">
                <a href="javascript:void(0);" role="button" class="btn-next btn btn-primary btn-block float-left mr-1 btn-confirm">
                  <i class="fa fa-file-alt mr-1"></i>
                  内容確認
                </a>
            </div>
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" data-dismiss="modal" role="button" class="btn btn-secondary btn-block float-left mr-1">
                <i class="fa fa-times-circle mr-1"></i>
                {{__('labels.close_button')}}
              </a>
            </div>
          </div>
        </div>
        <div class="carousel-item" id="confirm_form">
          <div class="row">
            <div class="col-12 bg-info p-2 pl-4">
              <i class="fa fa-file-invoice mr-1"></i>
              体験授業ご希望日の変更
            </div>
            <div class="col-6 p-3 font-weight-bold" >第１希望日時</div>
            <div class="col-6 p-3"><span id="trial_date_time1"></span></div>
            <div class="col-6 p-3 font-weight-bold" >第２希望日時</div>
            <div class="col-6 p-3"><span id="trial_date_time2"></span></div>
            <div class="col-6 p-3 font-weight-bold" >第３希望日時</div>
            <div class="col-6 p-3"><span id="trial_date_time3"></span></div>
          </div>
          <div class="row">
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-prev btn btn-secondary btn-block float-left mr-1">
                <i class="fa fa-arrow-circle-left mr-1"></i>
                戻る
              </a>
            </div>
            <div class="col-12 mb-1">
                <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="students_create">
                  体験授業ご希望日の変更する
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
