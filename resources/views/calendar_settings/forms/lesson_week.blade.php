<div class="col-10 mt-2">
  <div class="form-group">
    <label for="lesson_week" class="w-100">
      {{__('labels.week_day')}}
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    @foreach($attributes['lesson_week'] as $index => $name)
      @if(isset($calendar) && isset($calendar['students']) && count($calendar['students']) > 0 && ($calendar['students'][0]->user->has_tag('lesson_'.$index.'_time', 'disabled')===true || $calendar['teachers'][0]->user->has_tag('lesson_'.$index.'_time', 'disabled')===true))
      {{-- 生徒（複数の場合は一人目）と、講師の曜日が有効な曜日以外は無効--}}
        @continue
      @endif
      <label class="mx-2">
      <input type="radio" value="{{ $index }}" name="lesson_week" class="icheck flat-green" required="true" onChange="lesson_week_change();"
        @if(isset($_edit) && $_edit==true && $item->lesson_week == $index)
        checked
        @endif
      >{{$name}}曜
      </label>
    @endforeach
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
