<div class="col-12 my-2">
  <div class="form-group">
    <label for="type" class="w-100">
      グループ種別
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
      <div class="form-check ml-2 float-left">
          <input class="form-check-input icheck flat-green" type="radio" name="type" id="type_group" value="group" required="true"
          @if(isset($_edit) && $_edit==true && $item['type'] ==='group')
            checked
          @else(!isset($_edit) || $_edit!=true)
            checked
          @endif
          >
          <label class="form-check-label" for="type_group">
              グループ
          </label>
      </div>
      <div class="form-check ml-2 float-left">
          <input class="form-check-input icheck flat-green" type="radio" name="type" id="type_family" value="family" required="true"
          @if(isset($_edit) && $_edit==true && $item['type'] ==='family')
            checked
          @endif
          >
          <label class="form-check-label" for="type_family">
              ファミリー
          </label>
      </div>
    </div>
  </div>
</div>
