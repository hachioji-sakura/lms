@foreach(config('attribute.price') as $key=>$value)
<div class="col-6 col-md-6">
  <div class="form-group">
      <label for="teacher_character" class="w-100">
        {{__('labels.'.$key)}}
        <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
      </label>
    <input type="text" id="name_last" name="{{$key}}" class="form-control" placeholder="1500" inputtype="number"
      @if(isset($textbookPrices[$key]))
       value="{{$textbookPrices[$key]}}"
      @endif
    >
  </div>
</div>
@endforeach
