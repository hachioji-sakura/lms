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
          @component('students.forms.email', ['_edit' => $_edit, 'item'=>$item, 'attributes' => $attributes, 'is_label'=>$is_label]) @endcomponent
          <div class="row">
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" data-dismiss="modal" role="button" class="btn btn-secondary btn-block float-left mr-1">
                <i class="fa fa-times-circle mr-1"></i>
                {{__('labels.cancel_button')}}
              </a>
            </div>
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-next btn btn-primary btn-block float-left mr-1">
                <i class="fa fa-arrow-circle-right mr-1"></i>
                {{__('labels.next_button')}}
              </a>
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
