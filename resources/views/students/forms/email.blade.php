<div class="col-12">
  <div class="form-group">
    <label for="email">
      メールアドレス
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    @if(isset($is_label) && $is_label===true)
    <h5>{{$item['email']}}</h5>
    <input type="hidden" name="email" value="{{$item['email']}}" />
    @else
    <input type="text" id="email" name="email" class="form-control" placeholder="例：hachioji@sakura.com" required="true" inputtype="email">
    @endif
  </div>
</div>
