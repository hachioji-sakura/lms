<div class="col-12 piano_form">
  <div class="form-group">
    <label for="piano_level" class="w-100">
      @if(isset($_teacher) && $_teacher===true)
      担当可能な生徒につきまして
      @else
      ピアノのご経験につきまして教えてください
      @endif
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    @foreach($attributes['piano_level'] as $index => $name)
    <label class="mx-2">
      <input type="radio" id="piano_level_{{$index}}" value="{{ $index }}" name="piano_level" class="icheck flat-green" required="true"
      @if($_edit===true && isset($item) && $item->has_tag('piano_level', $index)===true)
      checked
      @endif
      >{{$name}}
    </label>
    @endforeach
  </div>
</div>
