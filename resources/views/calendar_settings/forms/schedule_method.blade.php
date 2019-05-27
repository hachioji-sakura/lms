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
          @if(isset($_edit) && $_edit==true && $item->schedule_method == $index)
          checked
          @endif
        >{{$name}}</label>
      @endif

    @endforeach
  </div>
</div>
<div class="col-6 mt-2 collapse" id="schedule_method_changed">
  <div class="form-group">
    <label for="lesson_week" class="w-100">
    週（毎月）
    </label>
    <div class="input-group">
      <select name='lesson_week_count' class="form-control" placeholder="場所" required="true">
        <option value="">(選択)</option>
        @for($i=1;$i<5;$i++)
          <option value="{{$i}}" @if(isset($_edit) && $_edit==true && $item['lesson_week_count'] == $i) selected @endif>第{{$i}}週</option>
        @endfor
      </select>
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
