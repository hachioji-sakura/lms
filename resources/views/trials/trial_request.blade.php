@include('trials.create_form')
<div class="direct-chat-msg">
  @if($_edit==true)
  <form method="POST"  action="/trials/{{$item->id}}">
    @method('PUT')
  @else
  <form method="POST"  action="/parents/{{$student_parent_id}}/trial_request">
  @endif
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
          @yield('trial_form_v2')
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
          @yield('lesson_week_form')
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
          <div class="row">
            <div class="col-12 bg-info p-2 pl-4 mb-4">
              <i class="fa fa-question-circle mr-1"></i>
              サービス向上のためアンケートをご記入ください
            </div>
            @component('students.forms.entry_milestone', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes]) @endcomponent
            @component('students.forms.remark', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes]) @endcomponent
            @component('students.forms.howto', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes]) @endcomponent
          </div>
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
          @component('trials.forms.confirm_form', ['attributes' => $attributes, 'is_trial' => true, 'is_already_registered_student' => true]) @endcomponent
          <div class="row">
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-prev btn btn-secondary btn-block float-left mr-1">
                <i class="fa fa-arrow-circle-left mr-1"></i>
                戻る
              </a>
            </div>
            <div class="col-12 mb-1">
              @if($_edit==true)
                <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="students_create">
                  申し込み内容を変更する
                    <i class="fa fa-caret-right ml-1"></i>
                </button>
              @else
              <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="students_create">
                  授業の申し込みを行う
                  <i class="fa fa-caret-right ml-1"></i>
              </button>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
@component('trials.forms.entry_page_script', ['_edit' => $_edit]) @endcomponent
