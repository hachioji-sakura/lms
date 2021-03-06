<div class="row">
  <div class="col-12 mb-2">
    <div class="row">
      <div class="col-4">
        <div class="description-block">
          <h5 class="description-header text-center">
            <a alt="teacher_name" href="/teachers/{{$teacher->id}}" target="_blank" class="">
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
  <div class="col-12">
    {{$teacher->get_tag_value('schedule_remark')}}
  </div>
  @if(!isset($is_detail) || $is_detail!==true)
  <div class="col-12 mb-2">
    <div class="description-block">
      <h5 class="description-header">
          <i class="fa fa-calendar-check mr-1"></i>
          希望通常スケジュール　ー空き情報ー
      </h5>
      <span class="description-text">
        <table class="table border-bottom">
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
              @if(count($teacher->match_schedule['result'][$index]) > 0)
                @if((isset($is_detail) && $is_detail==true))
                {{-- 詳細表示 --}}
                  @foreach($teacher->match_schedule['detail'][$index] as $time_slot)
                    @if($time_slot["slot"]>0)
                    <small class="badge badge-primary mx-2">
                      {{$time_slot["from"]}}～{{$time_slot["to"]}}
                      ({{$time_slot["slot"]}})
                    </small>
                    @endif
                  @endforeach
                @else
                  {{-- 簡易表示 --}}
                  @if(count($teacher->match_schedule['result'][$index])<2)
                  <small class="badge badge-danger mx-2">
                  @elseif(count($teacher->match_schedule['result'][$index])<3)
                  <small class="badge badge-warning mx-2">
                  @else
                  <small class="badge badge-primary mx-2">
                  @endif
                    空き{{count($teacher->match_schedule['result'][$index])}}
                  </small>
                @endif
              @else
              -
              @endif
          </td>
          @endforeach
        </tr>
        </table>
      </span>
    </div>
  </div>
  @else
  @endif
  {{$addon}}
</div>
