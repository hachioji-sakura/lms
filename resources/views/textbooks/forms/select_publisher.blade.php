<div class="col-12">
  <div class="form-group">
    <label for='place_floor_id' class="w-100">
      {{__('labels.publisher_name')}}
      <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
    </label>
    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text"><i class="fa fa-map-marker-alt"></i></span>
      </div>
      <select name='publisher_id' class="form-control select2">
        <option value="">
          {{__('labels.selectable')}}
        </option>
        @foreach($publishers as $publisher)
        <option value="{{ $publisher->id }}"
          @if(isset($textbook->publisher->id) && $publisher->id == $textbook->publisher->id)
          selected
          @endif>
          {{$publisher->name}}
        </option>
        @endforeach
      </select>
    </div>
  </div>
</div>
