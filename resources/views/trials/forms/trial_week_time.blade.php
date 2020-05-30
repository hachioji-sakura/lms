<div class="card card-widget mb-2">
  <div class="card-header">
    <i class="fa fa-calendar mr-1"></i>希望通常スケジュール
  </div>
  <div class="card-footer">
    <div class="row">
      <div class="col-12">
        <div class="form-group">
          <table class="table">
          <tr class="bg-gray">
            <th class="p-1 text-center border-right">時間帯 / 曜日</th>
            @foreach($attributes['lesson_week'] as $index => $name)
            <th class="p-1 text-center border-right lesson_week_label
            @if($index==="sat") text-primary
            @elseif($index==="sun") text-danger
            @endif
            " alt="{{$index}}">
               {{$name}}
            </th>
            @endforeach
          </tr>
          @foreach($attributes['lesson_time'] as $index => $name)
          <tr class="">
            <th class="p-1 text-center bg-gray text-sm lesson_week_time_label">{{$name}}</th>
            @foreach($attributes['lesson_week'] as $week_code => $week_name)
            <td class="p-1 text-center border-right" id="lesson_{{$week_code}}_time_{{$index}}_name">
              @if(isset($item) && $item->has_tag('lesson_'.$week_code.'_time', $index)===true)
                〇
              @else
                {{$item->has_tag('lesson_'.$week_code.'_time', $index)}}
              @endif
            </td>
            @endforeach
          </tr>
          @endforeach
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
