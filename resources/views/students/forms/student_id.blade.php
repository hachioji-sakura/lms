<div class="col-12">
  <div class="form-group">
    <label for="email" class="w-100">
      {{__('labels.student_login_id')}}
      @if(!(isset($_edit) && $_edit===true))
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
      @endif
    </label>
    @if(isset($_edit) && $_edit===true)
      <div class="w-100 email-edited">
        <span>{{$item->email}}</span>
        <a href="javascript:void(0);" onClick="email_form_edit()" class="btn btn-sm btn-success ml-2"><i class="fa fa-edit"></i></a>
      </div>
      <div class="w-100 email-edit" style="display:none;">
        <input type="text" id="student_id" name="email" class="form-control w-50 float-left" placeholder="任意の英数字"  required="true" inputtype="alnum" query_check="users/email" query_check_error="このログインIDはすでに使われています。別のIDを入力してください。" value="{{$item['email']}}">
        <a href="javascript:void(0);" onClick="email_form_edited()" class="btn btn-sm btn-success float-left mt-1 ml-2"><i class="fa fa-check"></i></a>
      </div>
    @else
      <input type="text" id="student_id" name="email" class="form-control" placeholder="任意の英数字" required="true" inputtype="alnum" query_check="users/email" query_check_error="このログインIDはすでに使われています。別のIDを入力してください。">
    @endif
  </div>
</div>
<script>
function email_form_edit(){
  $('div.email-edit').show();
  $('div.email-edited').hide();
}
function email_form_edited(){
  var email = $('div.email-edited span').text();
  var new_email = $('input[name="email"]').val();
  var is_check = false;
  console.log(email+'=='+new_email);
  if(email == new_email){
    is_check = true;
  }
  else if(front.validateFormValue('email_form')){
    is_check = true;
  }
  if(is_check){
    $('div.email-edit').hide();
    $('div.email-edited').show();
    $('div.email-edited span').html(new_email);
  }
  return is_check;
}

</script>
