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
          <div class="row">
            <div class="col-12 bg-info p-2 pl-4">
              <i class="fa fa-file-invoice mr-1"></i>
              体験授業お申込み内容
            </div>
            <div class="col-6 p-2 font-weight-bold" >ご希望のレッスン</div>
            <div class="col-6 p-2"><span id="lesson_name"></span></div>
            <div class="col-6 p-2 font-weight-bold" >第１希望日時</div>
            <div class="col-6 p-2"><span id="trial_date_time1"></span></div>
            <div class="col-6 p-2 font-weight-bold" >第２希望日時</div>
            <div class="col-6 p-2"><span id="trial_date_time2"></span></div>
            <div class="col-6 p-2 font-weight-bold" >第３希望日時</div>
            <div class="col-6 p-2"><span id="trial_date_time3"></span></div>
            <div class="col-6 p-2 font-weight-bold" >ご希望の校舎</div>
            <div class="col-6 p-2"><span id="lesson_place_name"></span></div>
          </div>
          {{--
          <div class="row">
            <div class="col-12 bg-info p-2 pl-4 mb-4">
              <i class="fa fa-calendar-alt mr-1"></i>
              通塾スケジュールにつきまして
            </div>
            <div class="col-6 p-2 font-weight-bold" >ご希望の授業回数</div>
            <div class="col-6 p-2">週<span id="lesson_week_count_name"></span></div>
            <div class="col-6 p-2 font-weight-bold" >ご希望の授業時間</div>
            <div class="col-6 p-2"><span id="course_minutes_name"></span></div>
          </div>
          --}}
          <div class="row">
            <div class="col-12 bg-info p-2 pl-4 subject_confirm">
              <i class="fa fa-pen-square mr-1"></i>
              塾の授業内容につきまして
            </div>
            <div class="col-12 p-2 font-weight-bold subject_confirm">
              ご希望の科目
            </div>
            <div class="col-12 subject_confirm">
              <div class="form-group">
                <table class="table table-striped">
                <tr class="bg-gray">
                  <th class="p-1 text-sm text-center">分類</th>
                  <th class="p-1 text-sm text-center">科目</th>
                  <th class="p-1 text-sm text-center">
                    補習授業
                  </th>
                  <th class="p-1 text-sm text-center">
                    受験対策
                  </th>
                </tr>
                @foreach(config('charge_subjects') as $grade => $subject_group)
                  @foreach($subject_group as $subject => $subject_data)
                    <?php $l1 = $loop->index; ?>
                    @isset($subject_data['items'])
                      @foreach($subject_data['items'] as $subject => $subject_name)
                        <tr class="grade-subject" alt="{{$grade}}">
                        @if($loop->index===0)
                        <th class="p-1 text-center bg-gray" rowspan={{count($subject_data['items'])}}>{{$subject_data['name']}}</th>
                        @endif
                        <th class="p-1 text-center bg-gray subject_name">{{$subject_name}}</th>
                        @foreach($attributes['lesson_subject_level'] as $index => $name)
                          @if($loop->index == 0)
                            @continue
                          @endif
                          <td class="text-center" id="{{$subject}}_level_{{$index}}_name">
                            -
                          </td>
                        </td>
                        @endforeach
                      </tr>
                      @endforeach
                    @else
                      <tr class="grade-subject" alt="{{$grade}}">
                      <th class="p-1 text-center bg-gray">{{$subject_data['name']}}</th>
                      <th class="p-1 text-center bg-gray subject_name">{{$subject_data['name']}}</th>
                      @foreach($attributes['lesson_subject_level'] as $index => $name)
                        @if($loop->index == 0)
                          @continue
                        @endif
                        <td class="text-center" id="{{$subject}}_level_{{$index}}_name">
                          -
                        </td>
                      @endforeach
                      </tr>
                    @endisset
                  @endforeach
                @endforeach
                </table>
              </div>
            </div>
            <div class="col-12 bg-info p-2 pl-4 english_talk_confirm">
              <i class="fa fa-comments mr-1"></i>
              英会話の授業内容につきまして
            </div>
            <div class="col-6 p-2 font-weight-bold english_talk_confirm" >ご希望の英会話講師</div>
            <div class="col-6 p-2 english_talk_confirm"><span id="english_teacher_name"></span></div>
            <div class="col-6 p-2 font-weight-bold english_talk_confirm" >ご希望の英会話講師</div>
            <div class="col-6 p-2 english_talk_confirm"><span id="english_talk_lesson_name"></span></div>
            <div class="col-6 p-2 font-weight-bold english_talk_confirm" >授業形式のご希望をお知らせください</div>
            <div class="col-6 p-2 english_talk_confirm"><span id="english_talk_course_type_name"></span></div>
            <div class="col-12 bg-info p-2 pl-4 piano_confirm">
              <i class="fa fa-music mr-1"></i>
              ピアノの授業内容につきまして
            </div>
            <div class="col-6 p-2 font-weight-bold piano_confirm" >ピアノのご経験につきまして</div>
            <div class="col-6 p-2 piano_confirm"><span id="piano_level_name"></span></div>
            <div class="col-12 bg-info p-2 pl-4 kids_lesson_confirm">
              <i class="fa fa-shapes mr-1"></i>
              習い事の授業内容につきまして
            </div>
            <div class="col-6 p-2 font-weight-bold kids_lesson_confirm" >ご希望の習い事につきましてお知らせください</div>
            <div class="col-6 p-2 kids_lesson_confirm"><span id="kids_lesson_name"></span></div>
            <div class="col-6 p-2 font-weight-bold kids_lesson_confirm" >授業形式のご希望をお知らせください</div>
            <div class="col-6 p-2 kids_lesson_confirm"><span id="kids_lesson_course_type_name"></span></div>
          </div>
          <div class="row">
            <div class="col-12 bg-info p-2 pl-4">
              <i class="fa fa-question-circle mr-1"></i>
              アンケート
            </div>
            <div class="col-6 p-2 font-weight-bold" >ご要望につきまして</div>
            <div class="col-6 p-2"><span id="remark"></span></div>
            <div class="col-6 p-2 font-weight-bold" >当塾をお知りになった方法は何でしょうか？</div>
            <div class="col-6 p-2"><span id="howto_name"></span></div>
            <div class="col-6 p-2 font-weight-bold howto_word_confirm collapse" >検索ワードをお答えください</div>
            <div class="col-6 p-2 howto_word_confirm collapse"><span id="howto_word"></span></div>
          </div>
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
