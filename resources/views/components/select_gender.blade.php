<div class="form-group">
  <label for="gender">
    性別
    <span class="right badge badge-danger ml-1">必須</span>
  </label>
  <div class="input-group">
    <div class="form-check">
        <input class="form-check-input flat-red" type="radio" name="gender" id="gender_2" value="2" required="true" @if(isset($item) && $item->gender===2) checked @endif>
        <label class="form-check-label" for="gender_2">
            女性
        </label>
    </div>
    <div class="form-check ml-2">
        <input class="form-check-input flat-red" type="radio" name="gender" id="gender_1" value="1" required="true" @if(isset($item) && $item->gender===1) checked @endif>
        <label class="form-check-label" for="gender_1">
            男性
        </label>
    </div>
  </div>
</div>
