<div class="col-12">
  <div class="form-group">
    <label for="parent_interview">
      体験授業当日に保護者様へ入会等の説明を行っております。<br>
      希望しますか？
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    <div class="input-group">
      <div class="form-check">
          <input class="form-check-input icheck flat-red" type="radio" name="parent_interview" id="parent_interview_t" value="true" required="true"
          @if($_edit==true && $item->has_tag('parent_interview', 'true')==true) checked @endif
          >
          <label class="form-check-label" for="parent_interview_t">
              {{__('labels.yes')}}
          </label>
      </div>
      <div class="form-check ml-2">
          <input class="form-check-input icheck flat-red" type="radio" name="parent_interview" id="parent_interview_f" value="false" required="true"
          @if($_edit==true && $item->has_tag('parent_interview', 'true')==false) checked @endif
          >
          <label class="form-check-label" for="parent_interview_f">
              {{__('labels.no')}}
          </label>
      </div>
    </div>
  </div>
</div>
