<div class="col-12 col-lg-6">
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
          @if(isset($item) && isset($item['start_hours']) && $item['start_hours']==$i)
            selected
          @endif
          >{{str_pad($i, 2, 0, STR_PAD_LEFT)}}時</option>
        @endfor
      </select>
      <select name="start_minutes" class="form-control float-left mr-1" required="true">
        <option value="">(選択)</option>
        @for ($i = 0; $i < 6; $i++)
        <option value="{{str_pad($i*10, 2, 0, STR_PAD_LEFT)}}"
        @if(isset($item) && isset($item['start_minutes']) && $item['start_minutes']==$i*10)
          selected
        @endif
        >{{str_pad($i*10, 2, 0, STR_PAD_LEFT)}}分</option>>
        @endfor
      </select>
    </div>
  </div>
</div>
