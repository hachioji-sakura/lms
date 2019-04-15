
@section('teacher_select_form')
  @if(count($candidate_teachers) > 0 && $select_lesson<1)
  @foreach($candidate_teachers as $lesson => $lesson_teachers)
  <ul class="mailbox-attachments clearfix row">
    <li class="col-12 bg-light" accesskey="" target="">
      <div class="row">
        <div class="col-12">
          {{$attributes['lesson'][$lesson]}}担当講師
        </div>
      </div>
    </li>
    @foreach($lesson_teachers as $teacher)
    <li class="col-6" accesskey="" target="">
      <div class="row">
        <div class="col-12 mb-2">
          <div class="row">
            <div class="col-4">
              <div class="description-block">
                <h5 class="description-header text-center">
                  <a href="/teachers/{{$teacher->id}}" target="_blank" class="">
                  <img src="{{$teacher->user->icon()}}" class="img-circle mw-64px" alt="User Image">
                  <br>
                    {{$teacher->name()}}
                  </a>
                </h5>
                <span class="description-text">
                  <a href="/teachers/{{$teacher->id}}/calendar" target="_blank" class="text-sm">
                    <i class="fa fa-calendar-alt mr-1"></i>
                    授業予定
                  </a>
                  @foreach($teacher->user->tags as $tag)
                    @if($user->role==="manager" && $tag->tag_key=="teacher_character")
                      <small class="badge badge-info mt-1 mr-1">
                        {{$tag->name()}}
                      </small>
                    @endif
                  @endforeach
                </span>
              </div>
            </div>
            <div class="col-4">
              <div class="description-block">
                <h5 class="description-header">
                  <i class="fa fa-check-circle mr-1"></i>
                  担当可
                </h5>
                <span class="description-text">
                  @if(count($teacher->enable_subject)<1)
                    <small class="badge badge-success mt-1 mr-1">
                      なし
                    </small>
                  @else
                    @foreach($teacher->enable_subject as $subject)
                    <small class="badge badge-{{$subject['style']}} mt-1 mr-1">
                      {{$subject["subject_name"]}}
                    </small>
                    @endforeach
                  @endif
                </span>
              </div>
            </div>
            <div class="col-4">
              <div class="description-block">
                <h5 class="description-header">
                  <i class="fa fa-times-circle mr-1"></i>
                  担当不可
                </h5>
                <span class="description-text">
                  @if(count($teacher->disable_subject)<1)
                    <small class="badge badge-success mt-1 mr-1">
                      なし
                    </small>
                  @else
                    @foreach($teacher->disable_subject as $subject)
                    <small class="badge badge-{{$subject['style']}} mt-1 mr-1">
                      {{$subject["subject_name"]}}
                    </small>
                    @endforeach
                  @endif
                </span>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12 mb-2">
        </div>
        <div class="col-12 mb-2">
          <div class="description-block">
            <h5 class="description-header">
                <i class="fa fa-calendar-check mr-1"></i>
                定期スケジュール　ー空き情報ー
            </h5>
            <span class="description-text">
              <table class="table table-striped border-bottom">
              <tr class="bg-gray">
                @foreach($attributes['lesson_week'] as $index => $name)
                <th class="p-1 text-center border-right lesson_week_label
                @if($index==="mon") border-left
                @elseif($index==="sat") text-primary
                @elseif($index==="sun") text-danger
                @endif
                " alt="{{$index}}">
                   {{$name}}
                </th>
                @endforeach
              </tr>
              <tr class="">
                @foreach($attributes['lesson_week'] as $index => $name)
                <td class="p-1 text-center @if($loop->index===0) border-left @endif border-right" id="">
                  @isset($teacher->match_schedule['count'][$index])
                    @if($teacher->match_schedule['count'][$index] > 0)
                    <small class="badge badge-primary mx-2">
                      空き{{$teacher->match_schedule['count'][$index]}}コマ
                    </small>
                    @else
                    -
                    @endif
                  @else
                  -
                  @endisset
                </td>
                @endforeach
              </tr>
              </table>
            </span>
          </div>
        </div>
        <div class="col-12 mb-2">
          <a href="/{{$domain}}/{{$item->id}}/to_calendar?teacher_id={{$teacher->id}}&lesson={{$lesson}}" role="button" class="btn btn-block btn-info">担当講師にする　<i class="fa fa-chevron-right ml-2"></i></a>
        </div>
      </div>
    </li>
    @endforeach
  </ul>
  @endforeach
  @else
  <div class="alert">
    <h4><i class="icon fa fa-exclamation-triangle"></i>データがありません</h4>
  </div>
  @endif
@endsection

@section('other_form')
<input type="hidden" name="teacher_id" value="{{$select_teacher_id}}">
<input type="hidden" name="start_time" value="">
<input type="hidden" name="end_time" value="">
@if($select_teacher_id > 0 && count($candidate_teachers) > 0)
<div class="row">
  <div class="col-4">
    <div class="description-block">
      <h5 class="description-header text-center">
        <a href="/teachers/{{$candidate_teachers[0]->id}}" target="_blank" class="">
        <img src="{{$candidate_teachers[0]->user->icon()}}" class="img-circle mw-64px" alt="User Image">
        <br>
          {{$candidate_teachers[0]->name()}}
        </a>
      </h5>
      <span class="description-text">
        @foreach($candidate_teachers[0]->user->tags as $tag)
          @if($user->role==="manager" && $tag->tag_key=="teacher_character")
            <small class="badge badge-info mt-1 mr-1">
              {{$tag->name()}}
            </small>
          @endif
        @endforeach
      </span>
    </div>
  </div>
  <div class="col-4">
    <div class="description-block">
      <h5 class="description-header">
        <i class="fa fa-check-circle mr-1"></i>
        担当可
      </h5>
      <span class="description-text">
        @if(count($candidate_teachers[0]->enable_subject)<1)
          <small class="badge badge-success mt-1 mr-1">
            なし
          </small>
        @else
          @foreach($candidate_teachers[0]->enable_subject as $subject)
          <small class="badge badge-{{$subject['style']}} mt-1 mr-1">
            {{$subject["subject_name"]}}
          </small>
          @endforeach
        @endif
      </span>
    </div>
  </div>
  <div class="col-4">
    <div class="description-block">
      <h5 class="description-header">
        <i class="fa fa-times-circle mr-1"></i>
        担当不可
      </h5>
      <span class="description-text">
        @if(count($candidate_teachers[0]->disable_subject)<1)
          <small class="badge badge-success mt-1 mr-1">
            なし
          </small>
        @else
          @foreach($candidate_teachers[0]->disable_subject as $subject)
          <small class="badge badge-{{$subject['style']}} mt-1 mr-1">
            {{$subject["subject_name"]}}
          </small>
          @endforeach
        @endif
      </span>
    </div>
  </div>
  <div class="col-12 mb-2">
    <div class="description-block">
      <h5 class="description-header">
          <i class="fa fa-calendar-check mr-1"></i>
          定期スケジュール　ー空き情報ー
      </h5>
      <span class="description-text">
        <table class="table table-striped border-bottom">
        <tr class="bg-gray">
          <?php $is_first=true; ?>
          @foreach($attributes['lesson_week'] as $index => $name)
            @isset($candidate_teachers[0]->match_schedule['count'][$index])
              @if($candidate_teachers[0]->match_schedule['count'][$index] > 0)
                <th class="p-1 text-center border-right lesson_week_label
                @if($is_first===true) border-left
                @elseif($index==="sat") text-primary
                @elseif($index==="sun") text-danger
                @endif
                " alt="{{$index}}">
                   {{$name}}
                </th>
                <?php $is_first=false; ?>
              @endif
            @endisset
          @endforeach
        </tr>
        <tr class="">
          <?php $is_first=true; ?>
          @foreach($attributes['lesson_week'] as $index => $name)
            @isset($candidate_teachers[0]->match_schedule['count'][$index])
              @if($candidate_teachers[0]->match_schedule['count'][$index] > 0)
              <td class="p-1 text-center @if($is_first===true) border-left @endif border-right" id="">
                @foreach($candidate_teachers[0]->match_schedule['detail'][$index] as $i => $dulation)
                  <small class="badge badge-primary mx-2">
                  {{$dulation['from']}}～{{$dulation['to']}}({{$dulation['slot']}})
                  </small>
                @endforeach
              </td>
              <?php $is_first=false; ?>
              @endif
            @endisset
          @endforeach
        </tr>
        </table>
      </span>
    </div>
  </div>
  <div class="col-6">
    <div class="description-block">
      <h5 class="description-header">
        <i class="fa fa-calendar-check mr-1"></i>
        体験授業予定（希望日時１）
      </h5>
      <span class="description-text">
        @if(count($candidate_teachers[0]->trial1) < 1)
          希望日時１は空いていません
        @else
          @foreach($candidate_teachers[0]->trial1 as $i=>$_list)
            @if($_list['free'])
            <div class="form-check ml-2">
              <input class="form-check-input icheck flat-green" type="radio" name="teacher_schedule" id="trial1_{{$i}}"
               required="true"
               value="{{$_list['start_time']}}_{{$_list['end_time']}}"
               dulation="{{$_list['dulation']}}"
               start_time="{{$_list['start_time']}}"
               end_time="{{$_list['end_time']}}"
               onChange="teacher_schedule_change(this)">
              <label class="form-check-label" for="trial1_{{$i}}">
                {{$_list['dulation']}}
              </label>
            </div>
            @else
            {{-- 空いてない --}}
            <div class="form-check ml-2">
              <label class="form-check-label" for="trial1_{{$i}}">
                <i class="fa fa-calendar-times mr-1"></i>
                {{$_list['dulation']}}
              </label>
            </div>
            @endif
          @endforeach
        @endif
      </span>
    </div>
  </div>
  <div class="col-6">
    <div class="description-block">
      <h5 class="description-header">
        <i class="fa fa-calendar-check mr-1"></i>
        体験授業予定（希望日時2）
      </h5>
      <span class="description-text">
        @if(count($candidate_teachers[0]->trial2) < 1)
          希望日時２は空いていません
        @else
          @foreach($candidate_teachers[0]->trial2 as $i=>$_list)
            @if($_list['free'])
            <div class="form-check ml-2">
              <input class="form-check-input icheck flat-green" type="radio" name="teacher_schedule" id="trial2_{{$i}}"
               required="true"
               value="{{$_list['start_time']}}_{{$_list['end_time']}}"
               dulation="{{$_list['dulation']}}"
               start_time="{{$_list['start_time']}}"
               end_time="{{$_list['end_time']}}"
               onChange="teacher_schedule_change(this)">
              <label class="form-check-label" for="trial2_{{$i}}">
                {{$_list['dulation']}}
              </label>
            </div>
            @else
            {{-- 空いてない --}}
            <div class="form-check ml-2">
              <label class="form-check-label" for="trial2_{{$i}}">
                <i class="fa fa-calendar-times mr-1"></i>
                {{$_list['dulation']}}
              </label>
            </div>
            @endif
          @endforeach
        @endif
      </span>
    </div>
  </div>
  <script >
  function teacher_schedule_change(obj){
    var _teacher_schedule = $('input[name=teacher_schedule]:checked');
    $('input[name=start_time]').val(_teacher_schedule.attr('start_time'));
    $('input[name=end_time]').val(_teacher_schedule.attr('end_time'));
  }
  </script>
  <div class="col-6 mt-2">
    <div class="form-group">
      <label for="course_type" class="w-100">
        授業形式
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      <div class="input-group" id="course_type_form">
        <div class="form-check">
            <input class="form-check-input icheck flat-green" type="radio" name="course_type" id="course_type_single" value="single" required="true" onChange="course_type_change()">
            <label class="form-check-label" for="course_type_single">
                マンツーマン
            </label>
        </div>
        <div class="form-check ml-2">
            <input class="form-check-input icheck flat-green" type="radio" name="course_type" id="course_type_group" value="group" required="true" onChange="course_type_change()" >
            <label class="form-check-label" for="course_type_group">
                グループレッスン
            </label>
        </div>
        @if(count($candidate_teachers[0]->brother_schedule)>0)
        <div class="form-check ml-2">
            <input class="form-check-input icheck flat-green" type="radio" name="course_type" id="course_type_family" value="family" required="true" onChange="course_type_change()" >
            <label class="form-check-label" for="course_type_family">
                ファミリー
            </label>
        </div>
        @endif
      </div>
    </div>
  </div>
  <script>
  function course_type_change(obj){
  }
  </script>
  <div class="col-6 mt-2">
    <div class="form-group">
      {{-- TODO:lesson_place＝申し込み時に入力された、場所概要から、lesson_place_flooreを絞り込む --}}
      <label for="lesson_place_floor" class="w-100">
        教室
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      <select name="lesson_place_floor" class="form-control" placeholder="場所" required="true">
        <option value="">(選択してください)</option>
        @foreach($attributes['lesson_place_floor'] as $index => $name)
          <option value="{{$index}}">{{$name}}</option>
        @endforeach
      </select>
    </div>
  </div>

  @if($select_lesson==1)
  <div class="col-12 mt-2">
    <div class="form-group">
      <label for="charge_subject" class="w-100">
        担当
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      <select name="charge_subject[]" class="form-control select2" placeholder="担当科目" required="true" multiple="multiple">
        <option value="">(選択してください)</option>
        @foreach($candidate_teachers[0]->enable_subject as $index=>$subject)
          <option value="{{$subject['subject_key']}}">{{$subject['subject_name']}}</option>
        @endforeach
      </select>
    </div>
  </div>
  @elseif($select_lesson==2)
  <div class="col-12 mt-2">
    <div class="form-group">
      <label for="english_talk_lesson" class="w-100">
        担当レッスン
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      <select name="english_talk_lesson[]" class="form-control select2" placeholder="担当科目" required="true" multiple="multiple">
        <option value="">(選択してください)</option>
        @foreach($candidate_teachers[0]->enable_subject as $index=>$subject)
          <option value="{{$subject['subject_key']}}">{{$subject['subject_name']}}</option>
        @endforeach
      </select>
    </div>
  </div>
  @elseif($select_lesson==3)
  <div class="col-12 mt-2">
    <div class="form-group">
      <label for="piano_lesson" class="w-100">
        担当レッスン
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      <select name="piano_lesson[]" class="form-control select2" placeholder="担当科目" required="true" multiple="multiple">
        <option value="">(選択してください)</option>
        @foreach($candidate_teachers[0]->enable_subject as $index=>$subject)
          <option value="{{$subject['subject_key']}}">{{$subject['subject_name']}}</option>
        @endforeach
      </select>
    </div>
  </div>
  @elseif($select_lesson==4)
  <div class="col-12 mt-2">
    <div class="form-group">
      <label for="kids_lesson" class="w-100">
        担当レッスン
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      <select name="kids_lesson[]" class="form-control select2" placeholder="担当科目" required="true" multiple="multiple">
        <option value="">(選択してください)</option>
        @foreach($candidate_teachers[0]->enable_subject as $index=>$subject)
          <option value="{{$subject['subject_key']}}">{{$subject['subject_name']}}</option>
        @endforeach
      </select>
    </div>
  </div>
  @endif
  <div class="col-12">
    <div class="form-group">
      <label for="howto" class="w-100">
        その他、講師に連絡する内容につきまして
        <span class="right badge badge-secondary ml-1">任意</span>
      </label>
      <textarea type="text" id="body" name="remark" class="form-control" placeholder="例：〇〇学校の受験希望をしている生徒様です。" ></textarea>
    </div>
  </div>
  <div class="col-12">
    <div class="form-group">
      <label for="matching_decide" class="w-100">
        講師を決めた理由は？
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      @foreach($attributes['matching_decide'] as $index => $name)
      <label class="mx-2">
        <input type="checkbox" value="{{ $index }}" name="matching_decide[]" class="icheck flat-green"  onChange="matching_decide_checkbox_change(this)" required="true">{{$name}}
      </label>
      @endforeach
    </div>
  </div>
  <div class="col-12 collapse matching_decide_word_form">
    <div class="form-group">
      <label for="matching_decide_word" class="w-100">
        その他の場合、理由を記述してください
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      <input type="text" id="matching_decide_word" name="matching_decide_word" class="form-control" placeholder="例：数学の受験対策を希望していたため" >
    </div>
  </div>
<input type="hidden" name="teacher_id" value="{{$candidate_teachers[0]->id}}">
@endif
<script>
function matching_decide_checkbox_change(obj){
  var is_other = $('input[type="checkbox"][name="matching_decide[]"][value="other"]').prop("checked");
  if(is_other){
    $(".matching_decide_word_form").collapse("show");
    $(".matching_decide_word_confirm").collapse("show");
  }
  else {
    $(".matching_decide_word_form").collapse("hide");
    $(".matching_decide_word_confirm").collapse("hide");
  }
}
</script>
</div>
@endsection
