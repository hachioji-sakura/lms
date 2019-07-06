<div class="col-12 english_talk_form">
  <div class="form-group">
    <label for="howto" class="w-100">
      @if(isset($_teacher) && $_teacher===true)
      担当可能な英会話レッスンにつきまして
      @else
      ご希望の英会話レッスンにつきましてお知らせください
      @endif
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    @foreach($attributes['english_talk_lesson'] as $index => $name)
    <label class="mx-2">
      <input type="checkbox" value="{{ $index }}" name="english_talk_lesson[]" class="icheck flat-green"
      @if($_edit===true && isset($item) && $item->has_tag('english_talk_lesson', $index)===true)
      checked
      @endif
      >{{$name}}
    </label>
    @endforeach
  </div>
</div>
