<div class="col-12">
  <div class="form-group">
    <label for="address" class="w-100">
      住所
      @if(!(isset($is_label) && $is_label===true))
      <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
      @endif
    </label>
    @if(isset($is_label) && $is_label===true)
    <span>{{$item['address']}}</span>
    @else
    <input type="text" id="address" name="address" class="form-control" placeholder="例：東京都八王子市〇〇１－２－３" inputtype="zenkaku"
    value="@if(isset($item) && isset($item['address'])){{$item['address']}}@endif"
    >
    @endif
  </div>
</div>
