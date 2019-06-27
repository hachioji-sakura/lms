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
        @foreach($attributes['places'] as $place)
          @foreach($place->floors as $floor)
          <option value="{{ $floor->id }}"
            @if(isset($item['place']) && $item['place'] == $floor->id)
             selected
            @endif>{{$floor->name}}
          </option>
          @endforeach
        @endforeach
      </select>
    </div>
  </div>
</div>
