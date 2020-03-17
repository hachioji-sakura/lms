<div class="col-12 col-md-4">
  <div class="form-group">
    <label for="post_no" class="w-100">
      {{__('labels.post_no')}}
      @if(!(isset($is_label) && $is_label===true))
      <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
      <span class="text-sm">{!!nl2br(__('messages.warning_telephone_not_hyphen'))!!}</span>
      @endif
    </label>
    @if(isset($is_label) && $is_label===true)
    <span>{{$item['post_no']}}</span>
    @else
    <input type="text" name="post_no" class="form-control" placeholder="例：1112222" inputtype="number" maxlength=7
    value="@if(isset($item) && isset($item['post_no'])){{$item['post_no']}}@endif"
    >
    @endif
  </div>
</div>
<div class="col-12 col-md-8">
  <div class="form-group">
    <label for="address" class="w-100">
      {{__('labels.address')}}
      @if(!(isset($is_label) && $is_label===true))
      <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
      @endif
    </label>
    @if(isset($is_label) && $is_label===true)
    <span>{{$item['address']}}</span>
    @else
    <input type="text" name="address" class="form-control" placeholder="例：東京都八王子市〇〇１－２－３" inputtype="zenkaku"
    value="@if(isset($item) && isset($item['address'])){{$item['address']}}@endif"
    >
    @endif
  </div>
</div>
