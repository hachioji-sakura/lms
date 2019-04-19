<div class="col-6 mt-2">
  <div class="form-group">
    <label for="lesson_place_floor" class="w-100">
      教室
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    <select name="lesson_place_floor" class="form-control" placeholder="場所" required="true">
      <option value="">(選択してください)</option>
      @foreach($attributes['lesson_place_floor'] as $index => $name)
        @foreach($item["tagdata"]["lesson_place"] as $tag_value=>$tag_name)
          @if(isset(config('lesson_place_floor')[$tag_value]) && isset(config('lesson_place_floor')[$tag_value][$index]))
          <option value="{{$index}}">{{$name}}</option>
          @endif
        @endforeach
      @endforeach
    </select>
  </div>
</div>
