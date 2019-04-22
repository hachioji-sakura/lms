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
    <div class="description-block">
      <h5 class="description-header">
          <i class="fa fa-calendar-check mr-1"></i>
          希望スケジュール　ー空き情報ー
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
                @if(isset($is_detail) && $is_detail==true)
                  @foreach($teacher->match_schedule['detail'][$index] as $time_slot)
                  <small class="badge badge-primary mx-2">
                    {{$time_slot["from"]}}～{{$time_slot["to"]}}
                    {{-- ({{$time_slot["slot"]}}) --}}
                  </small>
                  @endforeach
                @else
                @if($teacher->match_schedule['count'][$index]<2)
                <small class="badge badge-danger mx-2">
                @elseif($teacher->match_schedule['count'][$index]<3)
                <small class="badge badge-warning mx-2">
                @else
                <small class="badge badge-primary mx-2">
                @endif
                  空き{{$teacher->match_schedule['count'][$index]}}コマ
                </small>
                @endif
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
  @if(isset($is_detail) && $is_detail==true)
  <div class="col-12 mb-2">
    <div class="description-block">
      <h5 class="description-header">
          <i class="fa fa-calendar-times mr-1"></i>
          定期スケジュール
      </h5>
      <span class="description-text">
        <table class="table table-striped border-bottom">
        <tr class="bg-gray">
          <th class="p-1 text-center border-right ">
            曜日
          </th>
          <th class="p-1 text-center border-right ">
            時間
          </th>
          <th class="p-1 text-center border-right ">
            場所
          </th>
          <th class="p-1 text-center border-right ">
            内容
          </th>
        </tr>
        @foreach($attributes['lesson_week'] as $week_day => $week_name)
          {{-- 必要な曜日の予定のみ表示 --}}
          @if($teacher->match_schedule['count'][$week_day] > 0)
            @if(isset($teacher->user->calendar_setting()['week'][$week_day]))
              @foreach($teacher->user->calendar_setting()['week'][$week_day] as $setting)
              <tr>
                <td>
                  {{$week_name}}
                </td>
                <td>
                  {{$setting->timezone()}}
                </td>
                <td>
                  {{$setting->place()}}
                </td>
                <td>
                  {{$setting->work()}}
                </td>
              </tr>
              @endforeach
            @endif
          @endif
        @endforeach
        </table>
      </span>
    </div>
  </div>
  @endif
  {{$addon}}
</div>
