<div class="col-12 col-md-6">
  <div class="form-group">
    <label for="{{$prefix}}difficulty" class="w-100">
      {{__('labels.difficulty')}}
      @if($prefix !=='search_')
        <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
      @endif
    </label>
    <div class="input-group">
      <select
        id="{{$prefix}}difficulty"
        name="{{$prefix}}difficulty"
        class="form-control select2" width="100%">
        <option value=" ">{{__('labels.selectable')}}</option>
        @foreach(config('attribute.difficulty') as $key => $value)
          <option value="{{$key}}"
            @if(request()->search_difficulty == $key)
              selected
            @endif
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
