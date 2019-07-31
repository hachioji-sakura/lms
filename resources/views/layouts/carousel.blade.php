<div id="{{$domain}}_carousel" class="direct-chat-msg">
  <form method="POST"  action="/students/{{$item->id}}">
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    <div id="carousel_form" class="carousel slide" data-ride="carousel" data-interval="false">
      <div class="carousel-inner">
        <div class="carousel-item active">
          @yield('student_form')
          <div class="row">
            <div class="col-12 mb-1">
              <a href="/" role="button" class="btn btn-secondary btn-block float-left mr-1">
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
          @yield('survey_form')
          <div class="row">
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-prev btn btn-secondary btn-block float-left mr-1">
                <i class="fa fa-arrow-circle-left mr-1"></i>
                {{__('labels.back_button')}}
              </a>
            </div>
            <div class="col-12 mb-1">
                <a href="javascript:void(0);" role="button" class="btn-next btn btn-primary btn-block float-left mr-1">
                  <i class="fa fa-check-circle mr-1"></i>
                  {{__('labels.confirm_button')}}
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
                {{__('labels.back_button')}}
              </a>
            </div>
            @if(isset($user->role))
            <div class="col-12 mb-1">
              <a href="/" role="button" class="btn btn-secondary btn-block float-left mr-1">
                <i class="fa fa-times-circle mr-1"></i>
                {{__('labels.cancel_button')}}
              </a>
            </div>
            @endif
            <div class="col-12 mb-1">
                <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="students_create">
                  <i class="fa fa-plus-circle mr-1"></i>
                  {{__('labels.create_button')}}
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
  var form_data = util.getLocalData('carousel_form');
  base.pageSettinged("carousel_form", form_data);
  $('#carousel_form').carousel({ interval : false});

  //submit
  $("button.btn-submit").on('click', function(e){
    e.preventDefault();
    if(front.validateFormValue('carousel_form .carousel-item.active')){
      $("form").submit();
    }
  });

  //次へ
  $('.carousel-item .btn-next').on('click', function(e){
    if(front.validateFormValue('carousel_form .carousel-item.active')){
      var form_data = front.getFormValue('carousel_form');
      util.setLocalData('carousel_form', form_data);
      base.pageSettinged("confirm_form", form_data_adjust(form_data));
      $('#carousel_form').carousel('next');
      $('#carousel_form').carousel({ interval : false});
    }
  });
  //戻る
  $('.carousel-item .btn-prev').on('click', function(e){
    $('#carousel_form').carousel('prev');
    $('#carousel_form').carousel({ interval : false});
  });
  //確認画面用のパラメータ調整
  function form_data_adjust(form_data){
    form_data["email"] = $("input[name=email]").val();
    if(form_data["gender"]){
      form_data["gender_name"] = $("label[for='"+$("input[name='gender']:checked").attr("id")+"']").text().trim();
    }
    if(form_data["grade"]){
      form_data["grade_name"] = $('select[name=grade] option:selected').text().trim();
    }
    var _names = ["lesson_subject", "lesson_week", "lesson_time", "lesson_time_holiday", "lesson_place", "howto"];
    $.each(_names, function(index, value) {
      form_data[value+"_name"] = "";
      if(form_data[value+'[]']){
        $("input[name='"+value+'[]'+"']:checked").each(function() {
          form_data[value+"_name"] += $(this).parent().parent().text().trim()+'<br>';
        });
      }
    });
    return form_data;
  }
});
</script>
