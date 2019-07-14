
<div class="row mb-3">
  <div class="col-12 mb-2">
    <label for="name" class="w-100">
      {{__('labels.'.$domain)}}{{__('labels.name')}}
    </label>
    <span class="ml-3">
      {{$item->name()}}
    </span>
  </div>
  <div class="col-12 mb-2">
    <div class="form-group">
      <label for="start_date" class="w-100">
        {{__('labels.recess')}}{{__('labels.date')}}
        <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
      </label>
      <input type="text" id="start_date" name="start_date" class="form-control float-left w-40" required="true" uitype="datepicker" placeholder="例：{{date('Y/m/d')}}"
        minvalue="{{date('Y/m/d')}}"
      >
      <span class="float-left w-10 mx-2">～</span>
      <input type="text" id="start_date" name="start_date" class="form-control float-left w-40" required="true" uitype="datepicker" placeholder="例：{{date('Y/m/d')}}"
          minvalue="{{date('Y/m/d')}}"
          validate = "recess_date_check()"
        >
      </div>
  </div>
  <div class="col-12">
    <div class="form-group">
      <label for="remark" class="w-100">
        {{__('labels.recess')}}{{__('labels.reason')}}
        <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
      </label>
      <textarea type="text" id="body" name="remark" class="form-control" placeholder="" ></textarea>
    </div>
  </div>
  <div class="col-12 mt-2 mb-1">
    <div class="form-group">
      <input class="form-check-input icheck flat-green" type="checkbox" id="important_check" name="important_check" value="1" required="true" >
      <label class="form-check-label" for="important_check">
        {{__('labels.important_check')}}
      </label>
    </div>
  </div>
</div>



<script>
$(function(){
  base.pageSettinged("recess_form", null);
  //submit
  $("button.btn-submit").on('click', function(e){
    e.preventDefault();
    if(front.validateFormValue('recess_form')){
      var _confirm = $(this).attr("confirm");
      if(!util.isEmpty(_confirm)){
        if(!confirm(_confirm)) return false;
      }
      $("form").submit();
    }
  });
});
</script>
