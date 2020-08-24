<div class="col-12">
  <div class="form-group">
    <label for="lesson" class="w-100">
      @isset($title)
      {{$title}}
      @else
      ご希望のレッスン
      @endisset
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    @foreach($attributes['lesson'] as $index => $name)
    <label class="mx-1">
      <input type="checkbox" value="{{ $index }}" name="lesson[]" class="icheck flat-green" required="true"
      @if($_edit===true && isset($item) && $item->has_tag('lesson', $index)===true)
      checked
      @endif
      onChange="lesson_checkbox_change(this)">{{$name}}
    </label>
    @endforeach
  </div>
</div>
