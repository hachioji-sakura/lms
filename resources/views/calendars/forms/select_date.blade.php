<div class="col-7">
  <div class="form-group">
    <label for="start_date" class="w-100">
      日付
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
      </div>
      <input type="text" id="start_date" name="start_date" class="form-control float-left" required="true" uitype="datepicker"
      @if(isset($item) && isset($item['start_date'])) value="{{$item['start_date']}}" >  @endif
    </div>
  </div>
</div>
<div class="col-5">
  <div class="form-group">
    <label for="lesson_time" class="w-100">
      授業時間
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    <select name="lesson_time" class="form-control" placeholder="授業時間" required="true">
      @foreach($attributes['course_minutes'] as $index => $name)
      <option value="{{$index}}" @if(isset($item) && $item['lesson_time'] < $index) selected @endif>{{$name}}</option>
      @endforeach
    </select>
  </div>
</div>
