<div class="col-6 mt-2">
  <div class="form-group">
    {{-- TODO:lesson_place＝申し込み時に入力された、場所概要から、lesson_place_flooreを絞り込む --}}
    <label for="lesson_place_floor" class="w-100">
      教室
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    <select name="lesson_place_floor" class="form-control" placeholder="場所" required="true">
      <option value="">(選択してください)</option>
      @foreach($attributes['lesson_place_floor'] as $index => $name)
        <option value="{{$index}}">{{$name}}</option>
      @endforeach
    </select>
  </div>
</div>
