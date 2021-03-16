<div class="col-12 schedule_type schedule_type_office_work schedule_type_other">
  <div class="form-group">
    <label for="remark" class="w-100">
      {{__('labels.explain')}}
      <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
    </label>
    <textarea type="text" id="explain" name="explain" class="form-control" placeholder="" required="true">
      @if(isset($textbook))
        {{$textbook->explain}}
      @endif
    </textarea>
  </div>
</div>
