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
