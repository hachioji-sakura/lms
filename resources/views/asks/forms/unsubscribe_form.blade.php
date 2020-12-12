<input type="hidden" name="type" value="unsubscribe">
<input type="hidden" name="target_model_id" value="{{$item->id}}">
<input type="hidden" name="target_model" value="{{$domain}}">
<input type="hidden" name="charge_user_id" value="1">
<input type="hidden" name="target_user_id" value="{{$item->user_id}}">
<input type="hidden" name="end_date" value="">

<div class="row mb-3">
  <div class="col-12 mb-2">
    <label for="name" class="w-100">
      {{__('labels.students')}}{{__('labels.name')}}
    </label>
    <span class="ml-3">
      {{$item->name()}}
    </span>
  </div>
  @if($already_data!=null)
  <div class="col-12 mb-2">
    <div class="form-group">
      <label for="start_date" class="w-100">
        {{__('labels.unsubscribe')}}{{__('labels.date')}}
      </label>
      <span class="ml-3">
        {{$already_data["start_date"]}}
      </span>
      </div>
  </div>
  <div class="col-12 mb-2">
    <div class="form-group">
      <label for="start_date" class="w-100">
        {{__('labels.unsubscribe')}}{{__('labels.reason')}}
      </label>
      <span class="ml-3">
        {!!nl2br($already_data->body)!!}
      </span>
      </div>
  </div>
  @else
  <div class="col-12">
    <div class="form-group">
      <label for="start_date" class="w-100">
        {{__('labels.unsubscribe')}}{{__('labels.date')}}
        <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
      </label>
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text"><i class="fa fa-calendar"></i></span>
        </div>
        <input type="text" id="start_date" name="start_date" class="form-control float-left" required="true" uitype="datepicker" placeholder="例：{{date('Y/m/d', strtotime('+1 month'))}}"
          minvalue="{{date('Y/m/d', strtotime('+1 month'))}}"
        >
      </div>
    </div>
  </div>
  <div class="col-12">
    <div class="form-group">
      <label for="body" class="w-100">
        {{__('labels.unsubscribe')}}{{__('labels.reason')}}
        <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
      </label>
      <textarea type="text" id="body" name="body" class="form-control" placeholder="" ></textarea>
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
  @endif
</div>



<script>
$(function(){
  base.pageSettinged("unsubscribe_form", null);
  //submit
  $("button.btn-submit").on('click', function(e){
    e.preventDefault();
    if(front.validateFormValue('unsubscribe_form')){
      var _confirm = $(this).attr("confirm");
      if(!util.isEmpty(_confirm)){
        if(!confirm(_confirm)) return false;
      }
      $(this).prop("disabled",true);
      $("form").submit();
    }
  });
});
</script>
