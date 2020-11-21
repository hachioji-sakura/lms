@include($domain.'.matching_form')
<div class="direct-chat-msg">
  <form method="POST"  action="/{{$domain}}/{{$item->id}}/confirm">
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    @method('PUT')
    <div id="trials_confirm" class="carousel slide" data-ride="carousel" data-interval="false">
      @yield('matching_form')
      <div class="carousel-inner">
        <div class="carousel-item active">
          @yield('select_teacher_form')
          <div class="row">
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" data-dismiss="modal" role="button" class="btn btn-secondary btn-block float-left mr-1">
                <i class="fa fa-times-circle mr-1"></i>
                キャンセル
              </a>
            </div>
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-next btn btn-primary btn-block float-left mr-1">
                <i class="fa fa-arrow-circle-right mr-1"></i>
                次へ
              </a>
            </div>
          </div>
        </div>
        <div class="carousel-item">
          @yield('other_form')
          <div class="row">
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-prev btn btn-secondary btn-block float-left mr-1">
                <i class="fa fa-arrow-circle-left mr-1"></i>
                戻る
              </a>
            </div>
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" data-dismiss="modal" role="button" class="btn btn-secondary btn-block float-left mr-1">
                <i class="fa fa-times-circle mr-1"></i>
                キャンセル
              </a>
            </div>
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-next btn btn-primary btn-block float-left mr-1">
                <i class="fa fa-arrow-circle-right mr-1"></i>
                次へ
              </a>
            </div>
          </div>
        </div>
        <div class="carousel-item" id="confirm_form">
          @yield('confirm_form')
          <div class="row">
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-prev btn btn-secondary btn-block float-left mr-1">
                <i class="fa fa-arrow-circle-left mr-1"></i>
                戻る
              </a>
            </div>
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" data-dismiss="modal" role="button" class="btn btn-secondary btn-block float-left mr-1">
                <i class="fa fa-times-circle mr-1"></i>
                キャンセル
              </a>
            </div>
            <div class="col-12 mb-1">
                <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="trials_confirm">
                  <i class="fa fa-envelope mr-1"></i>
                    体験授業予定を連絡する
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
  var form_data = util.getLocalData('trials_confirm');
  base.pageSettinged("trials_confirm", form_data);
  $('#trials_confirm').carousel({ interval : false});
  //submit
  $("button.btn-submit").on('click', function(e){
    e.preventDefault();
    if(front.validateFormValue('trials_confirm .carousel-item.active')){
      util.removeLocalData('trials_confirm');
      $(this).prop("disabled",true);
      $("form").submit();
    }
  });

  //次へ
  $('.carousel-item .btn-next').on('click', function(e){
    $('body, html, .modal-body').scrollTop(0);
    if(front.validateFormValue('trials_confirm .carousel-item.active')){
      var form_data = front.getFormValue('trials_confirm');
      util.setLocalData('trials_confirm', form_data);
      base.pageSettinged("confirm_form", form_data_adjust(form_data));
      $('#trials_confirm').carousel('next');
      $('#trials_confirm').carousel({ interval : false});
    }
  });
  //戻る
  $('.carousel-item .btn-prev').on('click', function(e){
    $('body, html, .modal-body').scrollTop(0);
    $('#trials_confirm').carousel('prev');
    $('#trials_confirm').carousel({ interval : false});
  });
  //確認画面用のパラメータ調整
  function form_data_adjust(form_data){
    if(form_data['place']){
      form_data["place_name"] = $('select[name=place] option:selected').text().trim();
    }
    if(form_data["teacher_schedule"]){
      var _teacher_schedule = $('input[name=teacher_schedule]:checked');
      form_data["teacher_id"] = _teacher_schedule.attr('teacher_id');
      form_data['teacher_name'] = _teacher_schedule.attr('teacher_name');
      form_data["duration"] = _teacher_schedule.attr('duration');
      form_data["start_time"] = _teacher_schedule.attr('start_time');
      form_data["end_time"] = _teacher_schedule.attr('end_time');
    }

    var _names = ["matching_decide"];
    $.each(_names, function(index, value) {
      form_data[value+"_name"] = "";
      if(form_data[value+'[]']){
        $("input[name='"+value+'[]'+"']:checked").each(function() {
          form_data[value+"_name"] += $(this).parent().parent().text().trim()+'<br>';
        });
      }
    });
    console.log(form_data);
    return form_data;
  }

});
</script>
