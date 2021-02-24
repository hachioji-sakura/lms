<div class="form-group">
  <label for="locale" class="ml-2">
    {{__('labels.language')}}
  </label>
  <span class="badge badge-danger ml-1">{{__('labels.required')}}</span>
  <div class="input-group">
    <div class="form-check">
      <input class="frm-check-input icheck flat-green" type="radio" name="locale" id="locale_ja" value="ja" required="true"
      @if($_edit==false || ($_edit==true && $item->user->locale=='ja'))
      checked
      @endif
      >
      <label class="form-check-label" for="locale_ja">
        {{__('labels.ja')}}
      </label>
    </div>
    <div class="form-check">
      <input class="frm-check-input icheck flat-green" type="radio" name="locale" id="locale_en" value="en" required="true"
      @if($_edit==true && $item->user->locale=='en')
      checked
      @endif
      >
      <label class="form-check-label" for="locale_en">
        {{__('labels.en')}}
      </label>
    </div>
  </div>
</div>
