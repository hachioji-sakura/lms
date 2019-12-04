<div class="col-12 schedule_type schedule_type_other">
  <div class="form-group">
    <label for='work' class="w-100">
      {{__('labels.work')}}
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text"><i class="fa fa-wrench"></i></span>
      </div>
      <select name='work' class="form-control" required="true">
        @foreach($attributes['work'] as $index=>$name)
        @if(intval($index)>5 && intval($index)<9) @continue @endif
        <option value="{{ $index }}" @if($item['work']==$index) selected @endif>{{$name}}
        </option>
        @endforeach
      </select>
    </div>
  </div>
</div>
