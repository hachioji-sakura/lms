<div class="col-12 col-md-6">
  <div class="form-group">
    <label for="start_hours" class="w-100">
      {{__('labels.start_time')}}
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text"><i class="fa fa-clock"></i></span>
      </div>
      <select name="start_hours" class="form-control float-left mr-1" required="true">
        <option value="">{{__('labels.selectable')}}</option>
        @for ($i = 8; $i < 23; $i++)
          <option value="{{str_pad($i, 2, 0, STR_PAD_LEFT)}}"
          @if(isset($item) && isset($item['start_time']) && date('H', strtotime($item['start_time']))==str_pad($i, 2, 0, STR_PAD_LEFT))
            selected
          @elseif(isset($item) && isset($item['start_hours']) && $item['start_hours']==$i)
            selected
          @endif
          >{{str_pad($i, 2, 0, STR_PAD_LEFT)}}時</option>
        @endfor
      </select>
      <select name="start_minutes" class="form-control float-left mr-1" required="true">
        <option value="">{{__('labels.selectable')}}</option>
        @for ($i = 0; $i < 6; $i++)
        <option value="{{str_pad($i*10, 2, 0, STR_PAD_LEFT)}}"
        @if(isset($item) && isset($item['start_time']) && date('i', strtotime($item['start_time']))==str_pad($i*10, 2, 0, STR_PAD_LEFT))
          selected
        @elseif(isset($item) && isset($item['start_minutes']) && $item['start_minutes']==$i*10)
          selected
        @endif
        >{{str_pad($i*10, 2, 0, STR_PAD_LEFT)}}分</option>>
        @endfor
      </select>
    </div>
  </div>
</div>
<div class="col-12 col-md-6 schedule_type schedule_type_other schedule_type_office_work">
  <div class="form-group">
    <label for="end_hours" class="w-100">
      {{__('labels.end_time')}}
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text"><i class="fa fa-clock"></i></span>
      </div>
      <select name="end_hours" class="form-control float-left mr-1" required="true" greater="start_hours" greater_error="{{__('messages.validate_timezone_error')}}" not_equal="trial_start_time1" not_equal_error="{{__('messages.validate_timezone_error')}}">
        <option value="">{{__('labels.selectable')}}</option>
        @for ($i = 8; $i < 23; $i++)
          <option value="{{str_pad($i, 2, 0, STR_PAD_LEFT)}}"
          @if(isset($item) && isset($item['end_time']) && date('H', strtotime($item['end_time']))==str_pad($i, 2, 0, STR_PAD_LEFT))
            selected
          @elseif(isset($item) && isset($item['end_hours']) && $item['end_hours']==$i)
            selected
          @endif
          >{{str_pad($i, 2, 0, STR_PAD_LEFT)}}時</option>
        @endfor
      </select>
      <select name="end_minutes" class="form-control float-left mr-1" required="true">
        <option value="">{{__('labels.selectable')}}</option>
        @for ($i = 0; $i < 6; $i++)
        <option value="{{str_pad($i*10, 2, 0, STR_PAD_LEFT)}}"
        @if(isset($item) && isset($item['end_time']) && date('i', strtotime($item['end_time']))==str_pad($i*10, 2, 0, STR_PAD_LEFT))
          selected
        @elseif(isset($item) && isset($item['end_minutes']) && $item['end_minutes']==$i*10)
          selected
        @endif
        >{{str_pad($i*10, 2, 0, STR_PAD_LEFT)}}分</option>>
        @endfor
      </select>
    </div>
  </div>
</div>
