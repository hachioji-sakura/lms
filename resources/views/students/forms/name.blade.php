<div class="col-6 col-md-6">
  <div class="form-group">
    @if(isset($is_label) && $is_label===true)
    <label for="{{$prefix}}name_last" class="w-100">
      {{__('labels.name_last')}}
    </label>
    <span id="student_name_last">{{$item['name_last']}}</span>
    @else
    <label for="{{$prefix}}name_last">
      {{__('labels.name_last')}}
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    <input type="text" id="name_last" name="{{$prefix}}name_last" class="form-control" placeholder="例：八王子" required="true" inputtype="zenkaku"
      @if(isset($item) && !empty($item->name_last)) value="{{$item->name_last}}" @endif
      >
    @endif
  </div>
</div>
<div class="col-6 col-md-6">
  <div class="form-group">
    @if(isset($is_label) && $is_label===true)
    <label for="student_name_first" class="w-100">
      {{__('labels.name_first')}}
    </label>
    <span id="student_name_first">{{$item['name_first']}}</span>
    @else
    <label for="{{$prefix}}name_first">
      {{__('labels.name_first')}}
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    <input type="text" id="name_first" name="{{$prefix}}name_first" class="form-control" placeholder="例：太郎" required="true" inputtype="zenkaku"
      @if(isset($item) && !empty($item->name_first)) value="{{$item->name_first}}" @endif
    >
    @endif
  </div>
</div>
