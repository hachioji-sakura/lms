<div class="col-12 col-md-6">
  <div class="form-group">
    <label for='{{$prefix.$target_item}}' class="w-100">
      {{__('labels.'.$target_item.'_name')}}
      @if($prefix !=='search_')
        <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
      @endif
    </label>
    <div class="input-group">
      <select id="{{$prefix.$target_item}}" name='{{$prefix.$target_item}}_id' class="form-control select2" width="100%">
        <option value="">
          {{__('labels.selectable')}}
        </option>
        @foreach($collection as $model)
          <option value="{{ $model->id }}"
                  @if(request()->{$prefix.$target_item.'_id'} == $model->id)
                  selected
                  @endif
                  @if(isset($textbook->{$target_item}->id) && $model->id == $textbook->{$target_item}->id)
                  selected
            @endif
          >
            {{$model->name}}
          </option>
        @endforeach
      </select>
    </div>
  </div>
</div>

