<div class="col-12 col-lg-6 col-md-6">
  <div class="form-group">
    <label for="place" class="w-100">
      場所
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    <select name="place" class="form-control" placeholder="場所" required="true">
      @foreach($attributes['lesson_place_floor'] as $index => $name)
        <option value="{{ $index }}" @if(isset($_edit) && $item['place'] == $index) selected @endif>{{$name}}</option>
      @endforeach
    </select>
  </div>
</div>
