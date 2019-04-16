
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
      </div>
    </div>
  </div>
  <script>
  function course_type_change(obj){
  }
  </script>
  @component('trials.forms.lesson_place_floor', ['select_lesson' => $select_lesson, 'candidate_teachers' => $candidate_teachers[0], 'attributes' => $attributes]) @endcomponent
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
            <div class="form-check ml-2" id="trial1_select">
              <input class="form-check-input icheck flat-green" type="radio" name="teacher_schedule" id="trial1_{{$i}}"
               value="{{$_list['start_time']}}_{{$_list['end_time']}}"
               dulation="{{$_list['dulation']}}"
               start_time="{{$_list['start_time']}}"
               end_time="{{$_list['end_time']}}"
               onChange="teacher_schedule_change(this)"
               validate="teacher_schedule_validate('#trial1_select')">
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
            <div class="form-check ml-2" id="trial2_select">
              <input class="form-check-input icheck flat-green" type="radio" name="teacher_schedule" id="trial2_{{$i}}"
               value="{{$_list['start_time']}}_{{$_list['end_time']}}"
               dulation="{{$_list['dulation']}}"
               start_time="{{$_list['start_time']}}"
               end_time="{{$_list['end_time']}}"
               onChange="teacher_schedule_change(this)"
               validate="teacher_schedule_validate('#trial2_select')">
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
  function teacher_schedule_validate(obj){
    var start_time = $('input[name=start_time]').val();
    var end_time = $('input[name=end_time]').val();
    console.log("teacher_schedule_validate"+start_time+":"+end_time);
    var is_school = $('input[type="checkbox"][name="lesson[]"][value="1"]').prop("checked");
    if(!util.isEmpty(start_time) && !util.isEmpty(end_time)) return true;
    front.showValidateError(obj, '体験授業日時を指定してください');
    return false;
  }
  </script>
  @component('trials.forms.charge_subject', ['select_lesson' => $select_lesson, 'candidate_teachers' => $candidate_teachers[0], 'attributes' => $attributes]) @endcomponent

  <div class="col-12">
    <div class="form-group">
      <label for="remark" class="w-100">
        その他、講師に連絡する内容につきまして
        <span class="right badge badge-secondary ml-1">任意</span>
      </label>
      <textarea type="text" id="body" name="remark" class="form-control" placeholder="例：〇〇学校の受験希望をしている生徒様です。" ></textarea>
    </div>
  </div>

  @component('trials.forms.matching_decide', ['select_lesson' => $select_lesson, 'candidate_teachers' => $candidate_teachers[0], 'attributes' => $attributes]) @endcomponent
  <input type="hidden" name="teacher_id" value="{{$candidate_teachers[0]->id}}">
  <input type="hidden" name="lesson" value="{{$select_lesson}}">
@endif
</div>
@endsection
