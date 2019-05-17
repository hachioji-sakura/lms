<div class="col-6 mt-2">
  <div class="form-group">
    <label for="lesson_week" class="w-100">
      繰り返し
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    @foreach($attributes['schedule_method'] as $index => $name)
      @if(isset($calendar))
      {{-- この生徒（複数の場合は一人目）と、講師の曜日が有効な曜日を選択しに出す--}}
        <label class="mx-2">
        <input type="radio" value="{{ $index }}" name="schedule_method" class="icheck flat-green" required="true" onChange="schedule_method_change();"
          @if($item->schedule_method == $index)
          checked
          @endif
        >{{$name}}
        </label>
      @endif
    @endforeach
  </div>
</div>
<div class="col-6 mt-2 collapse" id="schedule_method_changed">
  <div class="form-group">
    <label for="lesson_week" class="w-100">
    週（毎月）
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    <div class="input-group">
      第
      <input type="text" inputtype="number" minvalue=1 maxvalue=5 maxlength=1 minlength=1
       name="lesson_week_count" class="form-control mx-1 w-50" required="true"
       placeholder="例：1"
        @if(isset($_edit) && $_edit===true && $item->lesson_week_count > 0)
        value="{{ $item->lesson_week_count }}"
        @endif
      >週
    </div>
  </div>
</div>
<script>
$(function(){
  schedule_method_change();
});
function schedule_method_change(){
  var schedule_method = $("input[name=schedule_method]:checked").val();
  console.log("schedule_method_change:"+schedule_method);
  if(schedule_method=="month"){
    $("#schedule_method_changed input").show();
    $("#schedule_method_changed").collapse("show");
  }
  else if(schedule_method=="week"){
    $("#schedule_method_changed input").hide();
    $("#schedule_method_changed").collapse("hide");
  }
}
</script>
