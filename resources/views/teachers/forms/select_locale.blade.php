<div class="form-group">
  <div class="input-group">
    <label for="locale" class="ml-2">
      {{__('labels.language')}}
    </label>
    <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    <div class="form-check">
      <input class="frm-check-input icheck flat-green" type="radio" name="locale" id="locale" value="ja" required="true" checked>
      <label class="form-check-label" for="locale">
        日本語
      </label>
    </div>
    <div class="form-check">
      <input class="frm-check-input icheck flat-green" type="radio" name="locale" id="locale" value="en" required="true">
      <label class="form-check-label" for="locale">
        English
      </label>
    </div>
  </div>
</div>
