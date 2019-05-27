@include('calendar_settings.create_form')
<div class="direct-chat-msg">
  @if(isset($_edit) && $_edit===true)
  <form id="edit" method="POST" action="/{{$domain}}/{{$item['id']}}">
    @method('PUT')
  @else
  <form id="edit" method="POST" action="/{{$domain}}">
  @endif
  @csrf
  @if(isset($origin))
    <input type="hidden" value="{{$origin}}" name="origin" />
  @endif
  @if(isset($student_id))
    <input type="hidden" value="{{$student_id}}" name="student_id" />
  @endif
  @if(isset($manager_id))
    <input type="hidden" value="{{$manager_id}}" name="manager_id" />
  @endif
    <div id="calendar_settings_entry" class="carousel slide" data-ride="carousel" data-interval="false">
      <div class="carousel-inner">
        <div class="carousel-item active">
          @yield('first_form')
          <div class="row">
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-next btn btn-primary btn-block float-left mr-1">
                次へ
                <i class="fa fa-arrow-circle-right ml-1"></i>
              </a>
            </div>
          </div>
        </div>
        <div class="carousel-item">
          @yield('second_form')
          <div class="row">
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-prev btn btn-secondary btn-block float-left mr-1">
                <i class="fa fa-arrow-circle-left mr-1"></i>
                戻る
              </a>
            </div>
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-next btn btn-primary btn-block float-left mr-1">
                次へ
                <i class="fa fa-arrow-circle-right ml-1"></i>
              </a>
            </div>
          </div>
        </div>
        <div class="carousel-item">
          @yield('third_form')
          <div class="row">
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-prev btn btn-secondary btn-block float-left mr-1">
                <i class="fa fa-arrow-circle-left mr-1"></i>
                戻る
              </a>
            </div>
            <div class="col-12 mb-1">
                <a href="javascript:void(0);" role="button" class="btn-next btn btn-primary btn-block float-left mr-1 btn-confirm">
                  <i class="fa fa-file-alt mr-1"></i>
                  内容確認
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
                <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="students_create">
                  この内容で
                  @if(isset($_edit) && $_edit==true)
                  更新する
                  @else
                  追加する
                  @endif
                    <i class="fa fa-caret-right ml-1"></i>
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
  var form_data = null;
  base.pageSettinged("calendar_settings_entry", form_data);
  $('#calendar_settings_entry').carousel({ interval : false});
  $("input[name='lesson[]']").change();
  //submit
  $("button.btn-submit").on('click', function(e){
    e.preventDefault();
    if(front.validateFormValue('calendar_settings_entry .carousel-item.active')){
      $("form").submit();
    }
  });

  //次へ
  $('.carousel-item .btn-next').on('click', function(e){
    var form_data = front.getFormValue('calendar_settings_entry');
    if(front.validateFormValue('calendar_settings_entry .carousel-item.active')){
      $('body, html').scrollTop(0);
      $('#calendar_settings_entry').carousel('next');
      $('#calendar_settings_entry').carousel({ interval : false});
    }

    $("ol.step li").removeClass("is-current");
    if($(this).hasClass('btn-confirm')){
      base.pageSettinged("confirm_form", form_data_adjust(form_data));
      $("ol.step #step_confirm").addClass("is-current");
    }
    else {
      $("ol.step #step_input").addClass("is-current");
    }
  });
  //戻る
  $('.carousel-item .btn-prev').on('click', function(e){
    $('body, html').scrollTop(0);
    $('#calendar_settings_entry').carousel('prev');
    $('#calendar_settings_entry').carousel({ interval : false});
  });
  //確認画面用のパラメータ調整
  function form_data_adjust(form_data){
    var _names = ["lesson", "lesson_place", "howto", "kids_lesson", "english_talk_lesson"];
    $.each(_names, function(index, value) {
      form_data[value+"_name"] = "";
      if(form_data[value+'[]']){
        $("input[name='"+value+'[]'+"']:checked").each(function() {
          form_data[value+"_name"] += $(this).parent().parent().text().trim()+'<br>';
        });
      }
    });

    var _t = $('input[name=teacher_id]').attr("alt");
    form_data["teacher_name"] = _t;


    var _snames = "";
    $('select[name="student_id[]"] option:selected').each(function(){
      var _sname = $(this).text().trim();
      _snames+=_sname+"<br>";
    });
    form_data["student_name"] = _snames;

    var _names = ["place", "student_group", "lesson_week_count"];
    $.each(_names, function(index, value) {
      if(form_data[value]){
        var _name = $('select[name='+value+'] option:selected').text().trim();
        form_data[value+"_name"] = _name;
      }
    });

    _names = ["course_minutes", "course_type", "schedule_method", "lesson_week"];
    $.each(_names, function(index, value) {
      form_data[value+"_name"] = [];
      if(form_data[value]){
        $("input[name='"+value+"']:checked").each(function() {
          form_data[value+"_name"].push($(this).parent().parent().text().trim()+'');
        });
        form_data[value+"_name"] = form_data[value+"_name"].join('<br>');
      }
    });

    var lesson = ($('input[name=lesson]:checked').val())|0;
    if(lesson==0){
      lesson = $('input[name=lesson]').attr("alt");
    }
    else {
      lesson = $('input[name=lesson]:checked').attr("alt");
    }
    //form_data["course_type_name"] = lesson+" "+form_data["course_type_name"];


    _snames = "";
    $('select[name="charge_subject[]"] option:selected').each(function(){
      var _sname = $(this).text().trim();
      _snames+=_sname+"<br>";
    });
    $('select[name="english_talk_lesson[]"] option:selected').each(function(){
      var _sname = $(this).text().trim();
      _snames+=_sname+"<br>";
    });
    $('select[name="kids_lesson[]"] option:selected').each(function(){
      var _sname = $(this).text().trim();
      _snames+=_sname+"<br>";
    });
    if(lesson==3) _snames = "ピアノ";
    form_data["subject_name"] = _snames;

    form_data["schedule_name"] = form_data["schedule_method_name"]+" "+form_data["lesson_week_count_name"]+" "+form_data["lesson_week_name"];

    return form_data;
  }
});
</script>
