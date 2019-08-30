<div class="col-12">
  <div class="form-group">
    <label for="address">
      住所
      <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
    </label>
    <input type="text" id="address" name="address" class="form-control" placeholder="例：東京都八王子市〇〇１－２－３" inputtype="zenkaku"
    value="@if(isset($item) && isset($item['address'])){{$item['address']}}@endif"
    >
  </div>
</div>
