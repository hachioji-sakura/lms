{{--
$prefix = kids_lesson | english_talk
$prefix_formで表示制御＋$prefix_course_typeにて、POST値を設定
  --}}
<div class="col-12 {{$prefix}}_form">
  <div class="form-group">
    <label for="{{$prefix}}_course_type" class="w-100">
      授業形式のご希望をお知らせください
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    @foreach($attributes['course_type'] as $index => $name)
    <label class="mx-2">
      <input type="radio" value="{{ $index }}" name="{{$prefix}}_course_type" class="icheck flat-green" required="true"
      @if($_edit===true && isset($item) && $item->has_tag($prefix.'_course_type', $index)===true)
      checked
      @endif
      >{{$name}}
    </label>
    @endforeach
  </div>
</div>
