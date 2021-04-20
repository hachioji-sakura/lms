<div class="col-12 col-md-6">
  <div class="form-group">
    <label for='{{$prefix}}publisher_id' class="w-100">
      {{__('labels.publisher_name')}}
      @if($prefix !=='search_')
        <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
      @endif
    </label>
    <div class="input-group">
      <select name="{{$prefix}}publisher_id" class="form-control select2" width="100%">
        <option value=" ">{{__('labels.selectable')}}</option>
        @foreach($publishers as $publisher)
          <option value="{{ $publisher->id }}"
            @if(request()->search_publisher_id == $publisher->id)
            selected
            @endif
            @if(isset($textbook->publisher->id) && $publisher->id == $textbook->publisher->id)
            selected
            @endif
          >
            {{$publisher->name}}
          </option>
        @endforeach
      </select>
    </div>
  </div>
</div>
