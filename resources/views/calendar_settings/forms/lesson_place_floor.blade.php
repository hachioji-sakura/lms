<div class="col-6 mt-2">
  <div class="form-group">
    <label for="lesson_place_floor" class="w-100">
      教室
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    <select name="lesson_place_floor" class="form-control" placeholder="場所" required="true">
      <option value="">(選択してください)</option>
      @foreach($attributes['lesson_place_floor'] as $index => $name)
        @if(isset($item["tagdata"]) && isset($item["tagdata"]["lesson_place"]))
          {{-- lesson_placeに所属する、lesson_place_floorのみ表示する --}}
          @foreach($item["tagdata"]["lesson_place"] as $tag_value=>$tag_name)
            @if(isset(config('lesson_place_floor')[$tag_value]) && isset(config('lesson_place_floor')[$tag_value][$index]))
            <option value="{{$index}}"
            @if(isset($calendar) && $calendar->place==$index)
              selected
            @endif
            >{{$name}}</option>
            @endif
          @endforeach
        @else
          <option value="{{$index}}"
          @if(isset($item->place) && $item->place==$index)
            selected
          @endif
          >{{$name}}</option>
        @endif
      @endforeach
    </select>
  </div>
</div>
