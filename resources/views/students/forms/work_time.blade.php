<div class="col-12">
  <div class="form-group">
    <label for="subject_level" class="w-100">
      曜日・時間帯
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    <table class="table table-striped">
    <tr class="bg-gray">
      <th class="p-1 text-center">時間帯 / 曜日</th>
      @foreach($attributes['lesson_week'] as $index => $name)
      <th class="p-1 text-center work_week_label" atl="{{$index}}">
         {{$name}}
      </th>
      @endforeach
    </tr>
    <tr class="">
      <th class="p-1 text-center work_week_time_label" alt="disabled">不可</th>
      @foreach($attributes['lesson_week'] as $week_code => $week_name)
      <td class="p-1 text-center">
        <input type="checkbox" value="disabled" name="{{$prefix}}_{{$week_code}}_time[]" class="icheck flat-grey lesson_week_time"  onChange="work_week_disabled_change(this)"
          @if(isset($item) && isset($item->user) && $item->user->has_tag($prefix.'_'.$week_code.'_time', 'disabled')===true)
          checked
          @elseif(isset($item) && isset($item->user) && $item->user->has_tag($prefix.'_'.$week_code.'_time')===false)
          checked
          @endif
         >
         {{$item->user->has_tag('work_'.$week_code.'_time', $index)}}
      </td>
      @endforeach
    </tr>
    @foreach($attributes['work_time'] as $index => $name)
    <tr class="">
      <th class="p-1 text-center bg-gray text-sm work_week_time_label">{{$name}}</th>
      @foreach($attributes['lesson_week'] as $week_code => $week_name)
      <td class="p-1 text-center">
        <input type="checkbox" value="{{ $index }}" name="{{$prefix}}_{{$week_code}}_time[]" class="icheck flat-green lesson_week_time"
        @if(isset($item) && isset($item->user) && $item->user->has_tag($prefix.'_'.$week_code.'_time', $index)==1)
       checked
        @endif
        >
      </td>
      @endforeach
    </tr>
    @endforeach
    </table>
    <script>
    function work_week_disabled_change(obj){
      var _name = $(obj).attr("name");
      var _checked = $(obj).prop("checked");
      if(_checked){
        //個別時間帯をすべてdisabled
        $('input[type="checkbox"][name="'+_name+'"]').each(function(i, e){
          if($(e).attr("value") !== "disabled") {
            $(this).prop('disabled', true);
            $(this).iCheck('uncheck');
            $(this).iCheck('disable');
          }
        });
      }
      else {
        $('input[type="checkbox"][name="'+_name+'"]').each(function(i, e){
          if($(e).attr("value") !== "disabled"){
            $(this).parent().removeClass('disabled');
            $(this).prop('disabled', false);
            $(this).iCheck('check');
            $(this).iCheck('enable');
          }
        });
      }
    }
    </script>
  </div>
</div>
