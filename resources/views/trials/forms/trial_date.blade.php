@if(isset($is_label) && $is_label==true)
  @if($item!=[])
    @for($i=1;$i<4;$i++)
    <div class="col-12 mt-2">
        <input type="hidden" name="trial_date_time{{$i}}" value = "{{date('m月d日', strtotime($item["trial_start_time".$i]))}} {{date('H', strtotime($item["trial_start_time".$i]))}}時～ {{date('H', strtotime($item["trial_start_time".$i]))}}時">
        <label for="start_date" class="w-100">
          第{{$i}}希望日時
        </label>
        <div class="input-group">
          {{__('labels.year_month_day', ['year' => date('Y', strtotime($item["trial_start_time".$i])), 'month' => date('m', strtotime($item["trial_start_time".$i])), 'day' => date('d', strtotime($item["trial_start_time".$i]))])}}
          {{date('H:i', strtotime($item["trial_start_time".$i]))}}～{{date('H:i', strtotime($item["trial_end_time".$i]))}}
        </div>
    </div>
    @endfor
  @endif
@else
  @for($i=1;$i<4;$i++)
  <div class="col-12 mt-2">
    <label for="start_date" class="w-100">
      第{{$i}}希望日時
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
  </div>
  <div class="col-12">
    <div class="row">
      <div class="col-12 col-md-6 mt-2">
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
          </div>
          <input type="text" name="trial_date{{$i}}" class="form-control mr-2" required="true" uitype="datepicker" placeholder="例：{{date('Y/m/d', strtotime('+3 day'))}}"
          @if($_edit===true)
           value="{{date('Y/m/d', strtotime($item["trial_start_time".$i]))}}"
          @else
           minvalue="{{date('Y/m/d', strtotime('+3 day'))}}"
          @endif
          >
        </div>
      </div>
      <div class="col-12 col-md-6 mt-2">
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class="fa fa-clock"></i></span>
          </div>
          <select name="trial_start_time{{$i}}" class="form-control mw-80px" required="true">
            <option value="">{{__('labels.selectable')}}</option>
            @for ($h = 8; $h < 23; $h++)
              <option value="{{$h}}"
              @if($_edit===true && $h==date('H', strtotime($item["trial_start_time".$i])))
              selected
              @endif
              >{{str_pad($h, 2, 0, STR_PAD_LEFT)}}</option>
            @endfor
          </select>
          <span class="mt-2 ml-2">時 ～</span>
          <select name="trial_end_time{{$i}}" class="form-control mw-80px" required="true" greater="trial_start_time{{$i}}" greater_error="{{__('messages.validate_timezone_error')}}" not_equal="trial_start_time{{$i}}" not_equal_error="{{__('messages.validate_timezone_error')}}" >
            <option value="">{{__('labels.selectable')}}</option>
            @for ($h = 8; $h < 23; $h++)
              <option value="{{$h}}"
              @if($_edit===true && $h==date('H', strtotime($item["trial_end_time".$i])))
              selected
              @endif
              >{{str_pad($h, 2, 0, STR_PAD_LEFT)}}</option>
              @endfor
          </select>
          <span class="mt-2 ml-2">時</span>
        </div>
      </div>
    </div>
  </div>
  @endfor
  <div class="col-12">
    <h6 class="text-sm p-1 pl-2 mt-2 bg-warning" >
      ※生徒様に最適な講師を紹介いたしますので、<br>
       体験授業ご希望日時について、幅広く教えてください。
    </h6>
  </div>
@endif
