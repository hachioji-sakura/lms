<div class="col-12 mt-2">
  <div class="form-group">
    <label for="lesson_place" class="w-100">
      @if(!isset($title) || empty($title))
      ご希望の校舎
      @else
      {{$title}}
      @endif
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    @foreach($attributes['places'] as $place)
    @if($place->name=='本校') @continue @endif
    <label class="mx-2 lesson_place">
      <input type="checkbox" value="{{$place->id}}" name="lesson_place[]" class="icheck flat-green" required="true"
      @if($_edit===true && isset($item) && $item->has_tag("lesson_place", $place->id))
      checked
      @endif
      >
      @if($place->is_home()==true)
      自宅（オンライン授業）
      @else
      {{$place->name}}
      <a href="javascript:void(0);"
        onclick="window.open('http://maps.google.co.jp/maps?q='+encodeURI('{{$place->address}}'));return false;">[MAP]
      </a>
      @endif
    </label>
    @endforeach
  </div>
</div>
