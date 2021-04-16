<div class="col-12 col-md-6">
  <div class="form-group">
    <label for='place_floor_id' class="w-100">
      {{__('labels.difficulty')}}
      <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
    </label>
    <div class="input-group">
      <select name='difficulty' class="form-control select2 w-100" width="100%">
        <option value="">
          {{__('labels.selectable')}}
        </option>
        @foreach(config('attribute.difficulty') as $key => $value)
          <option value="{{$key}}"
          @if(isset($textbook) && $textbook->difficulty == $key)
            selected
          @endif>
           {{$value}}
          </option>
        @endforeach
      </select>
    </div>
  </div>
</div>
