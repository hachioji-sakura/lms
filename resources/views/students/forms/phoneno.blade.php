<div class="col-12 col-lg-6 col-md-6">
  <div class="form-group">
    <label for="phone_no">
      連絡先
      <span class="right badge badge-danger ml-1">必須</span>
      <span class="text-sm">ハイフン(-)不要</span>
    </label>
    <input type="text" id="phone_no" name="phone_no" class="form-control" placeholder="例：09011112222" required="true" inputtype="number"
      value="@if(isset($item) && isset($item['phone_no'])){{$item['phone_no']}}@endif"
      >
  </div>
</div>
