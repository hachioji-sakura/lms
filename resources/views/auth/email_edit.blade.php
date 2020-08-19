<div id="email_edit" class="direct-chat-msg">
  <form method="POST"  action="/{{$domain}}/{{$item->id}}/email_edit">
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    <input type="hidden" name="user_id" value="{{$item->user_id}}"/ >
    @method('PUT')
    <div class="carousel slide" data-ride="carousel" data-interval="false">
      <div class="carousel-inner">
        <div class="carousel-item active">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label for="password">
                  {{__('labels.email')}}
                  <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
                </label>
                <input type="text" name="new_email" class="form-control" placeholder="{{$user->email}}" required="true" inputtype="email" query_check="users/email" query_check_error="{{__('messages.message_already_email')}}">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" data-dismiss="modal" role="button" class="btn btn-secondary btn-block float-left mr-1">
                <i class="fa fa-times-circle mr-1"></i>
                {{__('labels.cancel_button')}}
              </a>
            </div>
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-next btn-send-accesskey btn btn-primary btn-block float-left mr-1">
                <i class="fa fa-arrow-circle-right mr-1"></i>
                {{__('labels.next_button')}}
              </a>
            </div>
          </div>
        </div>
        <div class="carousel-item">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label for="password-confirm">
                  {{__('labels.verification_code')}}
                  <span class="right badge badge-danger ml-1">
                    {{__('labels.required')}}
                  </span>
                </label>
                <input type="text"  name="access_key" class="form-control" placeholder="" required="true" >
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <h4 class="bg-success p-3 text-sm">
                <i class="fa fa-info-circle"></i>
                {{__('messages.info_send_verification_code')}}
              </h4>
            </div>
          </div>
          <div class="row">
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-prev btn btn-secondary btn-block float-left mr-1">
                <i class="fa fa-arrow-circle-left mr-1"></i>
                {{__('labels.back_button')}}
              </a>
            </div>
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" data-dismiss="modal" role="button" class="btn btn-secondary btn-block float-left mr-1">
                <i class="fa fa-times-circle mr-1"></i>
                {{__('labels.cancel_button')}}
              </a>
            </div>
            <div class="col-12 mb-1">
                <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="email_edit">
                  <i class="fa fa-plus-circle mr-1"></i>
                  {{__('labels.update_button')}}
                </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>

<script>
$(function(){
  base.pageSettinged("email_edit", []);
  $('#email_edit').carousel({ interval : false});

  //submit
  $("button.btn-submit").on('click', function(e){
    e.preventDefault();
    if(front.validateFormValue('email_edit .carousel-item.active')){
      $(this).prop("disabled",true);
      $("form").submit();
    }
  });

  //次へ
  $('.carousel-item .btn-next').on('click', function(e){
    if(front.validateFormValue('email_edit .carousel-item.active')){
      var form_data = front.getFormValue('email_edit');
      if($(this).hasClass('btn-send-accesskey')){
        send_access_key(form_data);
      }
    }
  });
  //戻る
  $('.carousel-item .btn-prev').on('click', function(e){
    $('#email_edit').carousel('prev');
    $('#email_edit').carousel({ interval : false});
  });
  function send_access_key(form_data){
    console.log(form_data);
    service.getAjax(false,'/send_access_key', form_data,
      function(result, st, xhr) {
        if(result['status']===200){
          $('#email_edit').carousel({ interval : false});
          $('#email_edit').carousel('next');
        }
      },
      function(xhr, st, err) {
          messageCode = "error";
          messageParam= "\n"+err.message+"\n"+xhr.responseText;
          alert("システムエラーが発生しました\n"+messageParam);
      });
  }

});


</script>
