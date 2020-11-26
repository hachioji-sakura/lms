@extends('layouts.loginbox')
@section('title')
  ご入会希望に関する連絡
@endsection
@section('title_header')@yield('title')@endsection
@section('content')
@if($item->status=='new')

<form method="POST" action="/asks/{{$item['id']}}/status_update/commit">
  @csrf
  @method('PUT')
  <input type="text" name="dummy" style="display:none;" / >
  <input type="hidden" name="grade_name" value="{{$trial->student->grade()}}">
  <input type="hidden" name="key" value="{{$access_key}}">
  <input type="hidden" class="grade" name="grade" value="{{$trial->student->get_tag_value('grade')}}">

  <div id="trials_entry" class="carousel slide" data-ride="carousel" data-interval="false">
  <div class="carousel-inner">
    <div class="carousel-item active">
      <div class="row mb-4">
        <div class="col-12">
          <div class="form-group">
            <label for="status" class="w-100">
              {{(__('messages.message_hope_to_join'))}}
              <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
            </label>
            <div class="input-group">
              <div class="form-check">
                  <input class="form-check-input icheck flat-green" type="radio" name="status" id="status_commit" value="commit" required="true" onChange="status_radio_change()">
                  <label class="form-check-label" for="status_commit">
                      {{__('labels.yes')}}
                  </label>
              </div>
              <div class="form-check ml-2">
                  <input class="form-check-input icheck flat-green" type="radio" name="status" id="status_cancel" value="cancel" required="true"  onChange="status_radio_change()">
                  <label class="form-check-label" for="status_cancel">
                    {{__('labels.no')}}
                  </label>
              </div>
            </div>
          </div>
        </div>
        <script>
        function status_radio_change(){
          var is_cancel = $('input[type="radio"][name="status"][value="cancel"]').prop("checked");
          if(is_cancel){
            console.log("status_radio_change:hide");
            $(".status_commit_form").collapse("hide");
            $(".status_cancel_form").collapse("show");
          }
          else {
            console.log("status_radio_change:show");
            $(".status_commit_form").collapse("show");
            $(".status_cancel_form").collapse("hide");
          }
        }
        </script>
        <div class="col-12 mb-1">
          <a href="javascript:void(0);" role="button" class="btn-next btn btn-primary btn-block float-left mr-1">
            次へ
            <i class="fa fa-arrow-circle-right ml-1"></i>
          </a>
        </div>
      </div>
    </div>
    <div class="carousel-item">
      <div class="row collapse status_commit_form">
        <div class="col-12 bg-info p-2 pl-4 mb-4">
          <i class="fa fa-file-invoice mr-1"></i>
          ご入会お申込み内容
        </div>
        <?php
        $trial = $item->get_target_model_data();
        ?>
        <div class="col-12 mb-4">
            <label for="start_date" class="w-100">
              {{__('labels.schedule_start_hope_date')}}
              <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
            </label>
            <div class="input-group">
              <input type="text" name="schedule_start_hope_date" class="form-control float-left" required="true" uitype="datepicker" placeholder="例：{{date('Y/m/d')}}" minvalue="{{date('Y/m/d')}}"
              @if(isset($trial) && !empty($trial->schedule_start_hope_date))
                value ="{{date('Y/m/d', strtotime($trial->schedule_start_hope_date))}}"
              @endif
              >
            </div>
        </div>
        @component('students.forms.lesson', ['_edit'=>true, 'item'=>$trial,'attributes' => $attributes]) @endcomponent
        @component('students.forms.lesson_place', ['_edit'=>true, 'item'=>$trial, 'attributes' => $attributes]) @endcomponent

      </div>
      <div class="row collapse status_cancel_form">
        <div class="col-12 mb-1">
          <h4 class="bg-success p-3 text-sm">
            ご入会キャンセルの件、承知しました。<br>
            <br>
            また、生徒様の学習方法・進学について、
            お困りごとがありましたら、いつでも相談にのりますので、
            ご気軽にご連絡ください。<br>
            <br>
            どうぞよろしくお願い申し上げます。
          </h4>
        </div>
      </div>
      <div class="row">
        <div class="col-12 mb-1">
          <a href="javascript:void(0);" role="button" class="btn-prev btn btn-secondary btn-block float-left mr-1">
            <i class="fa fa-arrow-circle-left mr-1"></i>
            戻る
          </a>
        </div>
        <div class="col-12 mb-1 collapse status_commit_form">
          <a href="javascript:void(0);" role="button" class="btn-next btn btn-primary btn-block float-left mr-1">
            次へ
            <i class="fa fa-arrow-circle-right ml-1"></i>
          </a>
        </div>
        <div class="col-12 mb-1 collapse status_cancel_form">
            <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="trials_entry">
                送信する
                <i class="fa fa-caret-right ml-1"></i>
            </button>
        </div>
      </div>
    </div>

    <div class="carousel-item">
      <div class="row">
        <div class="col-12 bg-info p-2 pl-4 mb-4">
          <i class="fa fa-calendar-alt mr-1"></i>
          通塾スケジュールにつきまして
        </div>
        @component('students.forms.lesson_week_count', ['_edit'=>true, 'item'=>$trial, 'attributes' => $attributes]) @endcomponent
        @component('students.forms.course_minutes', ['_edit'=>true, 'item'=>$trial, '_teacher' => false, 'attributes' => $attributes]) @endcomponent
        @component('students.forms.work_time', ['_edit'=>true, 'item'=>$trial, 'prefix' => 'lesson', 'attributes' => $attributes, 'title' => 'ご希望の通塾曜日・時間帯']) @endcomponent
      </div>
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
        <div class="col-12 bg-info p-2 pl-4 mb-4 subject_form ">
          <i class="fa fa-pen-square mr-1"></i>
          塾の内容につきまして
        </div>
        @component('students.forms.subject', ['_edit'=>true, 'item'=>$trial, 'attributes' => $attributes, '_teacher' => false, 'category_display' => false, 'grade_display' => false]) @endcomponent
        <div class="col-12 bg-info p-2 pl-4 mb-4 english_talk_form ">
          <i class="fa fa-comments mr-1"></i>
          英会話の授業内容につきまして
        </div>
        @component('students.forms.english_teacher', ['_edit'=>true, 'item'=>$trial, 'attributes' => $attributes]) @endcomponent
        @component('students.forms.english_talk_lesson', ['_edit'=>true, 'item'=>$trial, 'attributes' => $attributes]) @endcomponent
        @component('students.forms.course_type', ['_edit'=>true, 'item'=>$trial, 'prefix'=>'english_talk', 'attributes' => $attributes]) @endcomponent
        <div class="col-12 bg-info p-2 pl-4 mb-4 piano_form ">
          <i class="fa fa-music mr-1"></i>
          ピアノの授業内容につきまして
        </div>
        @component('students.forms.piano_level', ['_edit'=>true, 'item'=>$trial, 'attributes' => $attributes]) @endcomponent
        <div class="col-12 bg-info p-2 pl-4 mb-4 kids_lesson_form ">
          <i class="fa fa-shapes mr-1"></i>
          習い事の授業内容につきまして
        </div>
        @component('students.forms.kids_lesson', ['_edit'=>true, 'item'=>$trial, 'attributes' => $attributes]) @endcomponent
      </div>
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
          ご入会にあたり、ご要望がありましたらご記入ください
        </div>
        @component('students.forms.entry_milestone', ['_edit'=>true, 'item'=>$trial, 'attributes' => $attributes]) @endcomponent
        @component('students.forms.remark', ['_edit'=>true, 'item'=>$trial, 'attributes' => $attributes]) @endcomponent
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
      @component('trials.forms.confirm_form', ['attributes' => $attributes]) @endcomponent
      <div class="row">
        <div class="col-12 mb-1">
          <a href="javascript:void(0);" role="button" class="btn-prev btn btn-secondary btn-block float-left mr-1">
            <i class="fa fa-arrow-circle-left mr-1"></i>
            戻る
          </a>
        </div>
        <div class="col-12 mb-1">
            <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="trials_entry">
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
