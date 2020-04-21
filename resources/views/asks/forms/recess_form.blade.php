<input type="hidden" name="type" value="recess">
<input type="hidden" name="target_model_id" value="{{$item->id}}">
<input type="hidden" name="target_model" value="{{$domain}}">
<input type="hidden" name="charge_user_id" value="1">
<input type="hidden" name="target_user_id" value="{{$item->user_id}}">
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
      {{__('labels.recess')}}{{__('labels.date')}}
    </label>
    <span class="ml-3">
      {{$already_data["duration"]}}
    </span>
    </div>
</div>
<div class="col-12 mb-2">
  <div class="form-group">
    <label for="start_date" class="w-100">
      {{__('labels.recess')}}{{__('labels.reason')}}
    </label>
    <span class="ml-3">
      {!!nl2br($already_data->body)!!}
    </span>
    </div>
</div>
@else
<div class="col-12 mb-2">
  <div class="form-group">
    <label for="start_date" class="w-100">
      {{__('labels.recess')}}{{__('labels.date')}}
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    <input type="text" id="start_date" name="start_date" class="form-control float-left w-40" required="true" uitype="datepicker" placeholder="例：{{date('Y/m/d')}}"
      minvalue="{{date('Y/m/d')}}"
      @if($already_data!=null) value="{{$already_data->start_date}}" @endif
    >
    <span class="float-left w-10 mx-2">～</span>
    <input type="text" id="end_date" name="end_date" class="form-control float-left w-40" required="true" uitype="datepicker" placeholder="例：{{date('Y/m/d')}}"
        minvalue="{{date('Y/m/d')}}"
        validate = "recess_date_check()"
        @if($already_data!=null) value="{{$already_data->end_date}}" @endif
      >
    </div>
</div>
<div class="col-12">
  <div class="form-group">
    <label for="body" class="w-100">
      {{__('labels.recess')}}{{__('labels.reason')}}
      <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
    </label>
    <textarea type="text" id="body" name="body" class="form-control" placeholder="" >@if($already_data!=null){{$already_data->body}}@endif</textarea>
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
function recess_date_check(){
  var start_date = $("input[name='start_date']").val();
  var end_date = $("input[name='end_date']").val();
  d = util.diffVal(end_date, start_date, 'date');
  if(d < 0 ){
    front.showValidateError("input[name='end_date']", '休会日付の設定が間違っています');
    return false;
  }
  var _sd = new Date(start_date+" 00:00:00");
  //2か月後
  _sd.setMonth(_sd.getMonth() + 2);
  var limit_date = util.parseDateToString(_sd);
  d = util.diffVal(limit_date, end_date, 'date');
  if(d < 0 ){
    front.showValidateError("input[name='end_date']", '休会は2か月までしかできません');
    return false;
  }
  return true;
}
</script>
