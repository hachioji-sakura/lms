@foreach($prices as $key=>$value)
  <div class="col-12 col-md-6">
  <div class="form-group">
      <label for="{{$key}}" class="w-100">
        {{$value}}
        <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
      </label>
    <input id="{{$key}}" type="text" name="{{$key}}" class="form-control" inputtype="number"
      @if(isset($textbook_prices[$key]))
       value="{{$textbook_prices[$key]}}"
      @endif
    >
  </div>
</div>
@endforeach
