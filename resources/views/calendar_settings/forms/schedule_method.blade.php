@if($_edit==true )
<input type="hidden" name="schedule_method" value="{{$item->schedule_method}}" >
<input type="hidden" name="schedule_method_name" value="{{$item->get_tag_name('schedule_method')}}" >
<div class="col-6 mt-2">
  <div class="form-group">
    <label for="schedule_method" class="w-100">
      {{__('labels.repeat')}}
    </label>
    <span>{{$item->schedule_method()}}</span>
  </div>
</div>
@else
<div class="col-6 mt-2">
  <div class="form-group">
    <label for="lesson_week" class="w-100">
      {{__('labels.repeat')}}
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    @foreach($attributes['schedule_method'] as $index => $name)
      {{-- この生徒（複数の場合は一人目）と、講師の曜日が有効な曜日を選択に出す--}}
        <label class="mx-2">
        <input type="radio" value="{{ $index }}" name="schedule_method" class="icheck flat-green" required="true" onChange="schedule_method_change();"
          @if(isset($_edit) && $_edit==true && $item->schedule_method == $index)
          checked
          @endif
        >{{$name}}</label>
    @endforeach
  </div>
</div>
@endif
<div class="col-6 mt-2 collapse" id="schedule_method_changed">
  <div class="form-group">
    <label for="lesson_week" class="w-100">
      {{__('labels.number_of_weeks')}}
    </label>
    <div class="input-group">
      <select name='lesson_week_count' class="form-control" required="true">
        <option value="">{{__('labels.selectable')}}</option>
        @for($i=1;$i<5;$i++)
          <option value="{{$i}}" @if(isset($_edit) && $_edit==true && $item['lesson_week_count'] == $i) selected @endif>{{__('labels.number_of_title')}}{{$i}}{{__('labels.calendar_button_week')}}</option>
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
  if(!schedule_method){
    schedule_method = $('input[type="hidden"][name="schedule_method"]').val();
  }
  if(!schedule_method){
    return false;
  }
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
