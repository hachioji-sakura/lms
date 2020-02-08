@if(isset($is_label) && $is_label==true)
  @if($item!=[])
  <input type="hidden" name="trial_date_time1" value = "{{date('m月d日', strtotime($item->trial_start_time1))}} {{date('H', strtotime($item->trial_start_time1))}}時～ {{date('H', strtotime($item->trial_end_time1))}}時">
  <input type="hidden" name="trial_date_time2" value = "{{date('m月d日', strtotime($item->trial_start_time2))}} {{date('H', strtotime($item->trial_start_time1))}}時～ {{date('H', strtotime($item->trial_end_time2))}}時">
  <input type="hidden" name="trial_date_time3" value = "{{date('m月d日', strtotime($item->trial_start_time3))}} {{date('H', strtotime($item->trial_start_time1))}}時～ {{date('H', strtotime($item->trial_end_time3))}}時">
  <div class="col-12 mt-2">
      <label for="start_date" class="w-100">
        第１希望日時
      </label>
      <div class="input-group">
        {{__('labels.year_month_day', ['year' => date('Y', strtotime($item->trial_start_time1)), 'month' => date('m', strtotime($item->trial_start_time1)), 'day' => date('d', strtotime($item->trial_start_time1))])}}
        {{date('H:i', strtotime($item->trial_start_time1))}}～{{date('H:i', strtotime($item->trial_end_time1))}}
      </div>
  </div>
  <div class="col-12 mt-2">
      <label for="start_date" class="w-100">
        第２希望日時
      </label>
      <div class="input-group">
        {{__('labels.year_month_day', ['year' => date('Y', strtotime($item->trial_start_time2)), 'month' => date('m', strtotime($item->trial_start_time2)), 'day' => date('d', strtotime($item->trial_start_time2))])}}
        {{date('H:i', strtotime($item->trial_start_time2))}}～{{date('H:i', strtotime($item->trial_end_time2))}}
      </div>
  </div>
  <div class="col-12 mt-2 mb-4">
      <label for="start_date" class="w-100">
        第３希望日時
      </label>
      <div class="input-group">
        {{__('labels.year_month_day', ['year' => date('Y', strtotime($item->trial_start_time3)), 'month' => date('m', strtotime($item->trial_start_time3)), 'day' => date('d', strtotime($item->trial_start_time3))])}}
        {{date('H:i', strtotime($item->trial_start_time3))}}～{{date('H:i', strtotime($item->trial_end_time3))}}
      </div>
  </div>
  @endif
@else

<div class="col-12 mt-2 col-md-4">
    <label for="start_date" class="w-100">
      第１希望日
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    <div class="input-group">
      <input type="text" name="trial_date1" class="form-control float-left" required="true" uitype="datepicker" placeholder="例：{{date('Y/m/d')}}" minvalue="{{date('Y/m/d')}}"
      @if($_edit===true)
       value="{{date('Y/m/d', strtotime($item->trial_start_time1))}}"
      @endif
      >
    </div>
</div>
<div class="col-12 mt-2 col-md-8">
  <label for="start_date" class="w-100">
    時間帯
    <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
  </label>
  <div class="input-group">
    <select name="trial_start_time1" class="form-control float-left mr-1 w-40" required="true">
      <option value="">{{__('labels.selectable')}}</option>
      @for ($i = 8; $i < 23; $i++)
        <option value="{{$i}}"
        @if($_edit===true && $i==date('H', strtotime($item->trial_start_time1)))
        selected
        @endif
        >{{str_pad($i, 2, 0, STR_PAD_LEFT)}}時</option>
      @endfor
    </select>
    <div class="w-10 text-center float-left mx-2">～</div>
    <select name="trial_end_time1" class="form-control float-left mr-1 w-40" required="true" greater="trial_start_time1" greater_error="{{__('messages.validate_timezone_error')}}" not_equal="trial_start_time1" not_equal_error="{{__('messages.validate_timezone_error')}}" >
      <option value="">{{__('labels.selectable')}}</option>
        @for ($i = 8; $i < 23; $i++)
        <option value="{{$i}}"
        @if($_edit===true && $i==date('H', strtotime($item->trial_end_time1)))
        selected
        @endif
        >{{str_pad($i, 2, 0, STR_PAD_LEFT)}}時</option>
        @endfor
    </select>
  </div>
</div>
<div class="col-12 mt-2 col-md-4">
  <label for="start_date" class="w-100">
    第２希望日
    <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
  </label>
  <div class="input-group">
    <input type="text" name="trial_date2" class="form-control float-left" required="true" uitype="datepicker" placeholder="例：{{date('Y/m/d')}}" minvalue="{{date('Y/m/d')}}"
    @if($_edit===true)
     value="{{date('Y/m/d', strtotime($item->trial_start_time2))}}"
    @endif
    >
  </div>
</div>
<div class="col-12 mt-2 col-md-8">
  <label for="start_date" class="w-100">
    時間帯
    <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
  </label>
  <div class="input-group">
    <select name="trial_start_time2" class="form-control float-left mr-1 w-40" required="true">
      <option value="">{{__('labels.selectable')}}</option>
      @for ($i = 8; $i < 23; $i++)
        <option value="{{$i}}"
        @if($_edit===true && $i==date('H', strtotime($item->trial_start_time2)))
        selected
        @endif
        >
        {{str_pad($i, 2, 0, STR_PAD_LEFT)}}時</option>
      @endfor
    </select>
    <div class="w-10 text-center float-left mx-2">～</div>
    <select name="trial_end_time2" class="form-control float-left mr-1 w-40" required="true" greater="trial_start_time2" greater_error="{{__('messages.validate_timezone_error')}}" not_equal="trial_start_time2" not_equal_error="{{__('messages.validate_timezone_error')}}" >
      <option value="">{{__('labels.selectable')}}</option>
      @for ($i = 8; $i < 23; $i++)
        <option value="{{$i}}"
        @if($_edit===true && $i==date('H', strtotime($item->trial_end_time2)))
        selected
        @endif
        >{{str_pad($i, 2, 0, STR_PAD_LEFT)}}時</option>
      @endfor
    </select>
  </div>
</div>
<div class="col-12 mt-2 col-md-4">
  <label for="start_date" class="w-100">
    第３希望日
    <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
  </label>
  <div class="input-group">
    <input type="text" name="trial_date3" class="form-control float-left" required="true" uitype="datepicker" placeholder="例：{{date('Y/m/d')}}" minvalue="{{date('Y/m/d')}}"
    @if($_edit===true)
     value="{{date('Y/m/d', strtotime($item->trial_start_time3))}}"
    @endif
    >
  </div>
</div>
<div class="col-12 mt-2 col-md-8  mb-4">
  <label for="start_date" class="w-100">
    時間帯
    <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
  </label>
  <div class="input-group">
    <select name="trial_start_time3" class="form-control float-left mr-1 w-40" required="true">
      <option value="">{{__('labels.selectable')}}</option>
      @for ($i = 8; $i < 23; $i++)
        <option value="{{$i}}"
        @if($_edit===true && $i==date('H', strtotime($item->trial_start_time3)))
        selected
        @endif
        >
        {{str_pad($i, 2, 0, STR_PAD_LEFT)}}時</option>
      @endfor
    </select>
    <div class="w-10 text-center float-left mx-2">～</div>
    <select name="trial_end_time3" class="form-control float-left mr-1 w-40" greater="trial_start_time3" greater_error="{{__('messages.validate_timezone_error')}}" not_equal="trial_start_time3" not_equal_error="{{__('messages.validate_timezone_error')}}" >
      <option value="">{{__('labels.selectable')}}</option>
      @for ($i = 8; $i < 23; $i++)
        <option value="{{$i}}"
        @if($_edit===true && $i==date('H', strtotime($item->trial_end_time3)))
        selected
        @endif
        >{{str_pad($i, 2, 0, STR_PAD_LEFT)}}時</option>
      @endfor
    </select>
  </div>
</div>
{{--　後まわし
<div class="col-12 mt-2 col-md-4">
  <label for="start_date" class="w-100">
    第４希望日
    <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
  </label>
  <div class="input-group">
    <input type="text" name="trial_date4" class="form-control float-left" uitype="datepicker" placeholder="例：{{date('Y/m/d')}}" minvalue="{{date('Y/m/d')}}"
    @if($_edit===true)
     value="{{date('Y/m/d', strtotime($item->trial_start_time4))}}"
    @endif
    >
  </div>
</div>
<div class="col-12 mt-2 col-md-8  mb-4">
  <label for="start_date" class="w-100">
    時間帯
    <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
  </label>
  <div class="input-group">
    <select name="trial_start_time4" class="form-control float-left mr-1 w-40" >
      <option value="">{{__('labels.selectable')}}</option>
      @for ($i = 8; $i < 23; $i++)
        <option value="{{$i}}"
        @if($_edit===true && $i==date('H', strtotime($item->trial_start_time4)))
        selected
        @endif
        >
        {{str_pad($i, 2, 0, STR_PAD_LEFT)}}時</option>
      @endfor
    </select>
    <div class="w-10 text-center float-left mx-2">～</div>
    <select name="trial_end_time4" class="form-control float-left mr-1 w-40" greater="trial_start_time4" greater_error="{{__('messages.validate_timezone_error')}}" not_equal="trial_start_time4" not_equal_error="{{__('messages.validate_timezone_error')}}" >
      <option value="">{{__('labels.selectable')}}</option>
      @for ($i = 8; $i < 23; $i++)
        <option value="{{$i}}"
        @if($_edit===true && $i==date('H', strtotime($item->trial_end_time4)))
        selected
        @endif
        >{{str_pad($i, 2, 0, STR_PAD_LEFT)}}時</option>
      @endfor
    </select>
  </div>
</div>
<div class="col-12 mt-2 col-md-4">
  <label for="start_date" class="w-100">
    第５希望日
    <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
  </label>
  <div class="input-group">
    <input type="text" name="trial_date5" class="form-control float-left" uitype="datepicker" placeholder="例：{{date('Y/m/d')}}" minvalue="{{date('Y/m/d')}}"
    @if($_edit===true)
     value="{{date('Y/m/d', strtotime($item->trial_start_time5))}}"
    @endif
    >
  </div>
</div>
<div class="col-12 mt-2 col-md-8  mb-4">
  <label for="start_date" class="w-100">
    時間帯
    <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
  </label>
  <div class="input-group">
    <select name="trial_start_time5" class="form-control float-left mr-1 w-40" >
      <option value="">{{__('labels.selectable')}}</option>
      @for ($i = 8; $i < 23; $i++)
        <option value="{{$i}}"
        @if($_edit===true && $i==date('H', strtotime($item->trial_start_time5)))
        selected
        @endif
        >
        {{str_pad($i, 2, 0, STR_PAD_LEFT)}}時</option>
      @endfor
    </select>
    <div class="w-10 text-center float-left mx-2">～</div>
    <select name="trial_end_time5" class="form-control float-left mr-1 w-40" greater="trial_start_time5" greater_error="{{__('messages.validate_timezone_error')}}" not_equal="trial_start_time5" not_equal_error="{{__('messages.validate_timezone_error')}}" >
      <option value="">{{__('labels.selectable')}}</option>
      @for ($i = 8; $i < 23; $i++)
        <option value="{{$i}}"
        @if($_edit===true && $i==date('H', strtotime($item->trial_end_time5)))
        selected
        @endif
        >{{str_pad($i, 2, 0, STR_PAD_LEFT)}}時</option>
      @endfor
    </select>
  </div>
</div>
--}}
<div class="col-12">
  <h6 class="text-sm p-1 pl-2 mt-2 bg-warning" >
    ※生徒様に最適な講師を紹介いたしますので、<br>
     体験授業ご希望日時について、幅広く教えてください。
  </h6>
</div>
@endif
