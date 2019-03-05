<div class="col-6 col-lg-6 col-md-6">
  <div class="form-group">
    <label for="{{$prefix}}kana_last">
      氏（カナ）
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    <input type="text" id="{{$prefix}}kana_last" name="{{$prefix}}kana_last" class="form-control" placeholder="例：ハチオウジ" required="true" inputtype="zenkakukana" @isset($item) value="{{$item->kana_last}}" @endisset>
  </div>
</div>
<div class="col-6 col-lg-6 col-md-6">
  <div class="form-group">
    <label for="{{$prefix}}kana_first">
      名（カナ）
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    <input type="text" id="{{$prefix}}kana_first" name="{{$prefix}}kana_first" class="form-control" placeholder="例：サクラ" required="true" inputtype="zenkakukana" @isset($item) value="{{$item->kana_first}}" @endisset>
  </div>
</div>
