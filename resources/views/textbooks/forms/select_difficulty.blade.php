<div class="col-12">
  <div class="form-group">
    <label for='place_floor_id' class="w-100">
      {{__('labels.difficulty')}}
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text"><i class="fa fa-map-marker-alt"></i></span>
      </div>
      <select name='place_floor_id' class="form-control" required="true">
        <option value="0">
          ー
        </option>
        @foreach(config('attribute.difficulty') as $key => $value)
          <option value="{{$key}}"
          @if(isset($textbook->difficulty) && $textbook->difficulty == $key)
            selected
          @endif>
          {{$value}}
          </option>
        @endforeach
      </select>
    </div>
  </div>
</div>
