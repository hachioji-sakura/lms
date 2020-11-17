@include($domain.'.create_form')
<div id="teachers_edit" class="direct-chat-msg">
  <form method="POST"  action="/{{$domain}}/{{$item->id}}">
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    @method('PUT')
    <div class="carousel slide" data-ride="carousel" data-interval="false">
      <div class="carousel-inner">
          <div class="carousel-item active">
            @yield('charge_form')
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
          <div class="carousel-item">
            @yield('lesson_week_form')
            <div class="row">
              <div class="col-12 mb-1">
                <a href="javascript:void(0);" role="button" class="btn-prev btn btn-secondary btn-block float-left mr-1">
                  <i class="fa fa-arrow-circle-left mr-1"></i>
                  {{__('labels.back_button')}}
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
          <div class="carousel-item">
            @yield('subject_form')
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
                  <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="teachers_edit">
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
  base.pageSettinged("teachers_edit", []);
  lesson_checkbox_change($('input[name="lesson[]"]'));
  $('#teachers_edit').carousel({ interval : false});
  //submit
  $("button.btn-submit").on('click', function(e){
    e.preventDefault();
    if(front.validateFormValue('teachers_edit .carousel-item.active')){
      $(this).prop("disabled",true);
      $("form").submit();
    }
  });

  //次へ
  $('.carousel-item .btn-next').on('click', function(e){
    if(front.validateFormValue('teachers_edit .carousel-item.active')){
      var form_data = front.getFormValue('teachers_edit');
      $('#teachers_edit').carousel('next');
      $('#teachers_edit').carousel({ interval : false});
    }
  });
  //戻る
  $('.carousel-item .btn-prev').on('click', function(e){
    $('#teachers_edit').carousel('prev');
    $('#teachers_edit').carousel({ interval : false});
  });
});
</script>
