<div class="col-12 english_form">
  <div class="form-group">
    <label for="english_teacher" class="w-100">
      英会話講師のご希望はございますか？
      <span class="right badge badge-secondary ml-1">任意</span>
    </label>
    @foreach($attributes['english_teacher'] as $index => $name)
    <label class="mx-2">
      <input type="radio" id="english_teacher_{{$index}}" value="{{ $index }}" name="english_teacher" class="icheck flat-green">{{$name}}
    </label>
    @endforeach
  </div>
</div>
