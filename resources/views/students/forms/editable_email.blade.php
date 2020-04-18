<div class="col-12 mb-2" id="email_form">
  <div class="form-group">
    <label for="email">
      メールアドレス
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    <div class="w-100 email-edited">
      <span>{{$item->email}}</span>
      <a href="javascript:void(0);" onClick="email_form_edit()" class="btn btn-sm btn-success ml-2"><i class="fa fa-edit"></i></a>
    </div>
    <div class="w-100 email-edit" style="display:none;">
      <input type="text" id="email" name="email" class="form-control w-50 float-left" placeholder="例：hachioji@sakura.com"  required="true" inputtype="email" query_check="users/email" query_check_error="{{__('messages.message_already_email')}}" value="{{$item['email']}}">
      <a href="javascript:void(0);" onClick="email_form_edited()" class="btn btn-sm btn-success float-left mt-1 ml-2"><i class="fa fa-check"></i></a>
    </div>
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
