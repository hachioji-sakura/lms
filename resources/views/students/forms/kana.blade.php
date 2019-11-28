<div class="col-6 col-md-6">
  <div class="form-group">
    @if(isset($is_label) && $is_label===true)
    <label for="{{$prefix}}kana_last" class="w-100">
      氏（カナ）
    </label>
    <span id="student_kana_last">{{$item['kana_last']}}</span>
    @else
    <label for="{{$prefix}}kana_last">
      名（カナ）
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    <input type="text" id="kana_last" name="{{$prefix}}kana_last" class="form-control" placeholder="例：ハチオウジ" required="true" inputtype="zenkakukana" @isset($item) value="{{$item['kana_last']}}" @endisset>
    @endif
  </div>
</div>
<div class="col-6 col-md-6">
  <div class="form-group">
    @if(isset($is_label) && $is_label===true)
    <label for="student_kana_first" class="w-100">
      氏（カナ）
    </label>
    <span id="student_kana_first">{{$item['kana_first']}}</span>
    @else
    <label for="{{$prefix}}kana_first">
      名（カナ）
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    <input type="text" id="kana_first" name="{{$prefix}}kana_first" class="form-control" placeholder="例：サクラ" required="true" inputtype="zenkakukana" @isset($item) value="{{$item['kana_first']}}" @endisset>
    @endif
  </div>
</div>
