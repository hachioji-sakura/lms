<div class="col-12">
  <div class="form-group">
    <label for="week_table" class="w-100">
      {{$title}}
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    <table class="table table-striped" id="week_table">
    <tr class="bg-gray">
      <th class="p-1 text-center">時間帯 / 曜日</th>
      @foreach($attributes['lesson_week'] as $index => $name)
      <th class="p-1 text-center {{$prefix}}_week_label
      @if($index==="sat") text-primary
      @elseif($index==="sun") text-danger
      @endif
      " alt="{{$index}}">
         {{$name}}
      </th>
      @endforeach
    </tr>
    <tr class="">
      <th class="p-1 text-center week_time_label" alt="disabled">不可</th>
      @foreach($attributes['lesson_week'] as $week_code => $week_name)
      <td class="p-1 text-center">
        <input type="checkbox" value="disabled" name="{{$prefix}}_{{$week_code}}_time[]" class="icheck flat-grey week_time"  onChange="week_change(this)"  validate="week_validate()"
          @if($_edit===true && isset($item) && $item->has_tag($prefix.'_'.$week_code.'_time', 'disabled')===true)
          checked
          @elseif($_edit===true && isset($item) && $item->has_tag($prefix.'_'.$week_code.'_time')===false)
          checked
          @elseif($_edit===false)
           checked
          @endif
         >
      </td>
      @endforeach
    </tr>
    @foreach($attributes[$prefix.'_time'] as $index => $name)
    <tr class="">
      <th class="p-1 text-center bg-gray text-sm week_time_label">{{$name}}</th>
      @foreach($attributes['lesson_week'] as $week_code => $week_name)
      <td class="p-1 text-center">
        <input type="checkbox" value="{{ $index }}" name="{{$prefix}}_{{$week_code}}_time[]" class="icheck flat-green week_time" onChange="week_change(this)"  validate="week_validate()"
        @if($_edit===true && isset($item) && $item->has_tag($prefix.'_'.$week_code.'_time', $index)==1)
       checked
       @elseif($_edit==false)
        disabled = "disabled"
        @endif
        >
      </td>
      @endforeach
    </tr>
    @endforeach
    </table>
    <script>
    function week_change(obj){
      var _name = $(obj).attr("name");
      var _val = $(obj).val();
      var _checked = $(obj).prop("checked");
      console.log('week_change');
      if(_checked && _val=='disabled'){
        //個別時間帯をすべてdisabled
        $('input[type="checkbox"][name="'+_name+'"][value!="disabled"]').each(function(i, e){
          if($(e).attr("value") !== "disabled") {
            $(this).prop('disabled', true);
            $(this).iCheck('uncheck');
            $(this).iCheck('disable');
          }
        });
      }
      else if(!_checked && _val=='disabled'){
        $('input[type="checkbox"][name="'+_name+'"][value!="disabled"]').each(function(i, e){
          if($(e).attr("value") !== "disabled"){
            $(this).parent().removeClass('disabled');
            $(this).prop('disabled', false);
            $(this).iCheck('check');
            $(this).iCheck('enable');
          }
        });
      }
    }
    function week_validate(){
      var _is_scceuss = false;
      if( $("input.week_time[type='checkbox']", $(".carousel-item.active")).length > 0){
        $("input.week_time[type='checkbox'][value!='disabled']:checked", $(".carousel-item.active")).each(function(index, value){
          var val = $(this).val();
          console.log(val);
          if(val!='disabled'){
            _is_scceuss = true;
          }
        });
        if(!_is_scceuss){
          front.showValidateError('#week_table', '希望の時間帯を１つ以上選択してください');
        }
      }
      else {
        return true;
      }
      return _is_scceuss;
    }
    </script>
  </div>
</div>
