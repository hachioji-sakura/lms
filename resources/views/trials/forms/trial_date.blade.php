{{--
<div class="col-12 mt-1">
  <h6 class="text-sm text-danger" >
    ※希望日時につきまして、ご指定いただいた時間帯の範囲にて、60分の授業を予定しております
  </h6>
</div>
--}}
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
    <select name="trial_end_time1" class="form-control float-left mr-1 w-40" required="true" greater="trial_start_time1" greater_error="時間帯範囲が間違っています" not_equal="trial_start_time1" not_equal_error="時間帯範囲が間違っています" >
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
    <select name="trial_end_time2" class="form-control float-left mr-1 w-40" required="true" greater="trial_start_time2" greater_error="時間帯範囲が間違っています" not_equal="trial_start_time2" not_equal_error="時間帯範囲が間違っています" >
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
    <select name="trial_end_time3" class="form-control float-left mr-1 w-40" required="true" greater="trial_start_time2" greater_error="時間帯範囲が間違っています" not_equal="trial_start_time2" not_equal_error="時間帯範囲が間違っています" >
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
<div class="col-12">
  <h6 class="text-sm p-1 pl-2 mt-2 bg-warning" >
    ※生徒様に最適な講師を紹介いたしますので、<br>
     体験授業ご希望日時について、幅広く教えてください。
  </h6>
</div>
