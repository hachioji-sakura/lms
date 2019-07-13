
<div class="row mb-3">
  <div class="col-6 mb-2">
    <label for="name" class="w-100">
      {{__('labels.'.$domain)}}{{__('labels.name')}}
    </label>
    <span class="ml-3">
      {{$item->name()}}
    </span>
  </div>
  <div class="col-12">
    <div class="form-group">
      <label for="start_date" class="w-100">
        {{__('labels.resume')}}{{__('labels.date')}}
        <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
      </label>
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text"><i class="fa fa-calendar"></i></span>
        </div>
        <input type="text" id="start_date" name="start_date" class="form-control float-left" required="true" uitype="datepicker" placeholder="例：{{date('Y/m/d')}}"
          minvalue="{{date('Y/m/d')}}"
        >
      </div>
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
  base.pageSettinged("resume_form", null);
  //submit
  $("button.btn-submit").on('click', function(e){
    e.preventDefault();
    if(front.validateFormValue('resume_form')){
      var _confirm = $(this).attr("confirm");
      if(!util.isEmpty(_confirm)){
        if(!confirm(_confirm)) return false;
      }
      $("form").submit();
    }
  });
});
</script>
