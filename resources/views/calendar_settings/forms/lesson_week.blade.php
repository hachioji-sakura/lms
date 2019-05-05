<div class="col-12 mt-2">
  <div class="form-group">
    <label for="lesson_week" class="w-100">
      曜日
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    @foreach($attributes['lesson_week'] as $index => $name)
      @if(isset($calendar))
      {{-- この生徒（複数の場合は一人目）と、講師の曜日が有効な曜日を選択しに出す--}}
        @if($calendar['students'][0]->user->has_tag('lesson_'.$index.'_time', 'disabled')===false && $calendar['teachers'][0]->user->has_tag('lesson_'.$index.'_time', 'disabled')===false)
        <label class="mx-2">
        <input type="radio" value="{{ $index }}" name="lesson_week" class="icheck flat-green" required="true" onChange="lesson_week_change();"
          @if($item->lesson_week == $index)
          checked
          @endif
        >{{$name}}曜
        </label>
        @endif
      @endif
    @endforeach
  </div>
</div>
<div class="col-12 mb-2">
  <div class="description-block">
    <h5 class="description-header">
        <i class="fa fa-calendar-times mr-1"></i>
        定期スケジュール
    </h5>
    <span class="description-text">
      <table id="lesson_week_schedule" class="table table-striped border-bottom" style="display:none;">
      <tr class="bg-secondary header">
        <th class="p-1 text-center border-right ">
          時間
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
            <tr class="{{$week_day}}">
              <td>
                {{$setting->timezone()}}
              </td>
              <td>
                <span class="text-xs mx-2">
                  <small class="badge badge-success mt-1 mr-1">
                    {{$setting->place()}}
                  </small>
                </span>
                <span class="text-xs mx-2">
                  <small class="badge badge-info mt-1 mr-1">
                    {{$setting->work()}}
                  </small>
                </span>
                @foreach($setting->subject() as $subject_key=>$label)
                <span class="text-xs mx-2">
                  <small class="badge badge-primary mt-1 mr-1">
                    {{$label}}
                  </small>
                </span>
                @endforeach
                @foreach($setting->details()["students"] as $member)
                  <a target="_blank" href="/students/{{$member->user->details('students')->id}}" class="mx-1">
                    {{$member->user->details('students')->name}}
                  </a>
                @endforeach
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
<script>
function lesson_week_change(){
  var week = $("input[name=lesson_week]:checked").val();
  $("#lesson_week_schedule tr").hide();
  $("#lesson_week_schedule tr.header").show();
  $("#lesson_week_schedule tr."+week).show();
  $("#lesson_week_schedule").show();
}
</script>
