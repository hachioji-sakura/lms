<div class="col-12 col-lg-6 col-md-6">
  <div class="form-group">
    <label for="start_date" class="w-100">
      日付
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
      </div>
      <input type="text" id="start_date" name="start_date" class="form-control float-left" required="true" uitype="datepicker" placeholder="例：{{date('Y/m/d')}}"
      @if(isset($item) && isset($item['start_time']))
        value="{{date('Y/m/d', strtotime($item['start_time']))}}"
      @elseif(isset($item) && isset($item['start_date']))
        value="{{$item['start_date']}}"
      @else
        minvalue="{{date('Y/m/d')}}"
      @endif
      >
    </div>
  </div>
</div>
<div class="col-12 col-lg-6 col-md-6">
  <div class="form-group">
    <label for="start_hours" class="w-100">
      開始時刻
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text"><i class="fa fa-clock"></i></span>
      </div>
      <select name="start_hours" class="form-control float-left mr-1" required="true">
        <option value="">(選択)</option>
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
        <option value="">(選択)</option>
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
