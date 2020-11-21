<div class="col-12">
  <div class="form-group">
    <label for="{{$prefix}}week_table" class="w-100">
      {{$title}}
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    <table class="table" id="{{$prefix}}week_table">
    <tr class="bg-gray">
      <th class="p-1 text-center">時間帯 / 曜日</th>
      @foreach($attributes['lesson_week'] as $index => $name)
      <th class="p-1 text-center {{$prefix}}_week_label
      @if($index==="sat") text-primary
      @elseif($index==="sun") text-danger
      @endif
      " alt="{{$index}}">
         {{$name}}
         <input type="checkbox" name="{{$prefix}}_{{$index}}_time[]" value="disabled" class="week_time" style="display:none;"></input>
      </th>
      @endforeach
    </tr>
    <?php
      $attribute_name = $prefix.'_time';
      if($prefix=='trial' || $prefix=='season_lesson'){
        $attribute_name = 'lesson_time';
      }
      $display = true;
      if(isset($from_time_index)) $display = false;
    ?>
    @foreach($attributes[$attribute_name] as $index => $name)
    <?php
      if(isset($from_time_index) && $display==false && $index==$from_time_index){
        $display = true;
      }
    ?>
    @if($display!=true) @continue @endif
    <tr class="">
      <th class="p-1 text-center bg-gray text-sm week_time_label">
        {{$name}}
      </th>
      @foreach($attributes['lesson_week'] as $week_code => $week_name)
      <td class="p-1 text-center">
        <input type="checkbox" value="{{ $index }}" name="{{$prefix}}_{{$week_code}}_time[]" class="icheck flat-green {{$prefix}}week_time"  validate="week_validate('{{$prefix}}')"
        @if($_edit===true && isset($item) && $item->has_tag($prefix.'_'.$week_code.'_time', $index)==1)
       checked
       @elseif($_edit==false)
        @endif
        >
      </td>
      @endforeach
    </tr>
    <?php
      if(isset($to_time_index) && $display==true && $index==$to_time_index){
        $display = false;
      }
    ?>
    @endforeach
    </table>
    <script>
    function week_validate(prefix){
      var _is_scceuss = false;
      if( $("input."+prefix+"week_time[type='checkbox']").length > 0){
        var _week_input = [];
        $("input."+prefix+"week_time[type='checkbox']").each(function(index, value){
          var val = $(this).val();
          var name = $(this).attr('name');
          var checked = $(this).prop('checked');
          if(!_week_input[name])  _week_input[name] = false;
          if(val!='disabled' && checked==true){
            _is_scceuss = true;
            _week_input[name] = true;
          }
        });
        console.log(prefix+":"+_week_input[name]);
        for(var key in _week_input){
          if(_week_input[key] == false){
            $("input."+prefix+"week_time[name='"+key+"'][value='disabled']").prop('checked', true);
          }
        }
        if(!_is_scceuss){
          front.showValidateError('#'+prefix+'week_table', '希望の時間帯を１つ以上選択してください');
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
