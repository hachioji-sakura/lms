@foreach(__('labels.prices') as $key=>$value)
<div class="col-6 col-md-6">
  <div class="form-group">
      <label for="teacher_character" class="w-100">
        {{__('labels.prices.'.$key)}}
        <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
      </label>
    <input type="text" id="name_last" name="{{$key}}" class="form-control" inputtype="number"
      @if(isset($textbook_prices[$key]))
       value="{{$textbook_prices[$key]}}"
      @endif
    >
  </div>
</div>
@endforeach
