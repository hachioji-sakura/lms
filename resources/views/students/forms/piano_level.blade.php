<div class="col-12 piano_form">
  <div class="form-group">
    <label for="piano_level" class="w-100">
      ピアノのご経験について教えてください
      <span class="right badge badge-secondary ml-1">任意</span>
    </label>
    @foreach($attributes['piano_level'] as $index => $name)
    <label class="mx-2">
      <input type="radio" id="piano_level_{{$index}}" value="{{ $index }}" name="piano_level" class="icheck flat-green">{{$name}}
    </label>
    @endforeach
  </div>
</div>
