<div class="col-6 mt-2">
  <div class="form-group">
    <label for="lesson_place_floor" class="w-100">
      {{__('labels.place')}}
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    <select name="place_floor_id" class="form-control" required="true">
      <option value="">{{__('labels.selectable')}}</option>
      @foreach($attributes['places'] as $place)
        @foreach($place->floors as $floor)
        @if(isset($item["tagdata"]) && isset($item["tagdata"]["lesson_place"]) && isset($item["tagdata"]["lesson_place"][$place->id]))
          <option value="{{$floor->id}}"
            @if(isset($calendar) && $calendar->place_floor_id==$floor->id)
              selected
            @elseif(isset($item->place_floor_id) && $item->place_floor_id==$floor->id)
              selected
            @elseif($loop->index==0)
              selected
            @endif
          >{{$floor->name()}}</option>
          @endif
        @endforeach
      @endforeach
    </select>
  </div>
</div>
