<div class="col-6 col-lg-6">
  <div class="form-group">
    <label for='place' class="w-100">
      場所
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text"><i class="fa fa-map-marker-alt"></i></span>
      </div>
      <select name='place' class="form-control" placeholder="場所" required="true">
        <option value="">(選択)</option>
        @foreach($attributes['lesson_place_floor'] as $index => $name)
          <option value="{{ $index }}" @if(isset($_edit) && $_edit==true && $item['place'] == $index) selected @endif>{{$name}}</option>
        @endforeach
      </select>
    </div>

  </div>
</div>
