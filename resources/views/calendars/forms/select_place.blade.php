<div class="col-12 col-lg-6">
  <div class="form-group">
    <label for='place_floor_id' class="w-100">
      {{__('labels.place')}}
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text"><i class="fa fa-map-marker-alt"></i></span>
      </div>
      <select name='place_floor_id' class="form-control" required="true">
        @foreach($attributes['places'] as $place)
          @foreach($place->floors as $floor)
          <option value="{{ $floor->id }}"
            @if(isset($item['place_floor_id']) && $item['place_floor_id'] == $floor->id)
             selected
            @endif>{{$floor->name()}}
          </option>
          @endforeach
        @endforeach
      </select>
    </div>
  </div>
</div>
