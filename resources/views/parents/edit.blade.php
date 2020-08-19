<?php
$parent = $item;
$is_label = true;
if($user->role=="manager") $is_label = false;
?>
@include($domain.'.create_form')
<div id="parents_edit" class="direct-chat-msg">
  <form method="POST"  action="/{{$domain}}/{{$item->id}}">
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    @method('PUT')
    <div class="carousel slide" data-ride="carousel" data-interval="false">
      <div class="carousel-inner">
        <div class="carousel-item active">
          @yield('parent_form')
          <div class="row">
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" data-dismiss="modal" role="button" class="btn btn-secondary btn-block float-left mr-1">
                <i class="fa fa-times-circle mr-1"></i>
                {{__('labels.cancel_button')}}
              </a>
            </div>
            <div class="col-12 mb-1">
                <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="students_edit">
                  <i class="fa fa-edit mr-1"></i>
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
  base.pageSettinged("parents_edit", []);
  $('#parents_edit').carousel({ interval : false});
  //submit
  $("button.btn-submit").on('click', function(e){
    e.preventDefault();
    if(front.validateFormValue('parents_edit .carousel-item.active')){
      $(this).prop("disabled",true);
      $("form").submit();
    }
  });

  //次へ
  $('.carousel-item .btn-next').on('click', function(e){
    if(front.validateFormValue('parents_edit .carousel-item.active')){
      var form_data = front.getFormValue('parents_edit');
      $('#parents_edit').carousel('next');
      $('#parents_edit').carousel({ interval : false});
    }
  });
  //戻る
  $('.carousel-item .btn-prev').on('click', function(e){
    $('#parents_edit').carousel('prev');
    $('#parents_edit').carousel({ interval : false});
  });
});
</script>
