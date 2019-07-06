<div class="col-6 col-lg-6">
  <div class="form-group">
    <label for='status' class="w-100">
      ステータス
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text"><i class="fa fa-cog"></i></span>
      </div>
      <select name='status' class="form-control" placeholder="ステータス" required="true">
        <option value="">{{__('labels.selectable')}}</option>
        @foreach(config('attribute.calendar_status') as $index => $name)
          <option value="{{ $index }}" @if($item['status']==$index) selected @endif>{{$name}}
          </option>
        @endforeach
      </select>
    </div>

  </div>
</div>
