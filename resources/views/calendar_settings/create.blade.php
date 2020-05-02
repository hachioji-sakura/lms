@include('calendar_settings.create_form')
<div class="direct-chat-msg">
  @if(isset($_edit) && $_edit===true)
  <form id="edit" method="POST" action="/{{$domain}}/{{$item['id']}}">
    @method('PUT')
  @else
  <form id="edit" method="POST" action="/{{$domain}}">
  @endif
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
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
            @if($item->work==9)
            <div class="col-12 mb-1">
              <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="students_create">
                  {{__('labels.update_button')}}
                  <i class="fa fa-caret-right ml-1"></i>
              </button>
            </div>
            @else
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-next btn btn-primary btn-block float-left mr-1">
                {{__('labels.next_button')}}
                <i class="fa fa-arrow-circle-right ml-1"></i>
              </a>
            </div>
            @endif
          </div>
        </div>
        <div class="carousel-item">
          @yield('second_form')
          <div class="row">
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-prev btn btn-secondary btn-block float-left mr-1">
                <i class="fa fa-arrow-circle-left mr-1"></i>
                {{__('labels.back_button')}}
              </a>
            </div>
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-next btn btn-primary btn-block float-left mr-1">
                {{__('labels.next_button')}}
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
                {{__('labels.back_button')}}
              </a>
            </div>
            <div class="col-12 mb-1">
                <a href="javascript:void(0);" role="button" class="btn-next btn btn-primary btn-block float-left mr-1 btn-confirm">
                  <i class="fa fa-file-alt mr-1"></i>
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
            <div class="col-12 mb-1">
                <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="students_create">
                  @if(isset($_edit) && $_edit==true)
                  {{__('labels.update_button')}}
                  @else
                  {{__('labels.create_button')}}
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

    var _sh = $('select[name=start_hours] option:selected').val();
    var _sm = $('select[name=start_minutes] option:selected').val();
    form_data["start_time"] = _sh+":"+_sm;

    var _t = $('input[name=teacher_id]').attr("alt");
    form_data["teacher_name"] = _t;

    if($('select[name="student_id[]"] option:selected').length > 0){
      var _snames = "";
      $('select[name="student_id[]"] option:selected').each(function(){
        var _sname = $(this).text().trim();
        _snames+=_sname+"<br>";
      });
      form_data["student_name"] = _snames;
    }
    var _names = ["place_floor_id", "student_group", "lesson_week_count"];
    $.each(_names, function(index, value) {
      if(form_data[value] && $('select[name='+value+'] option:selected').length>0){
        var _name = $('select[name='+value+'] option:selected').text().trim();
        form_data[value+"_name"] = _name;
        if(value=='place_floor_id'){
          if($('input[name="is_online"]').prop('checked')){
            form_data[value+"_name"] += '/ <i class="fa fa-globe"></i>{{__('labels.online')}}';
          }
        }
      }
    });

    _names = ["course_minutes", "course_type", "schedule_method", "lesson_week"];
    $.each(_names, function(index, value) {
      if(form_data[value] && $("input[name='"+value+"']:checked").length>0){
        form_data[value+"_name"] = [];
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

    form_data["schedule_name"] = form_data["schedule_method_name"];
    if(form_data["lesson_week_count_name"] && form_data["schedule_method"]=="month"){
      form_data["schedule_name"] += " "+form_data["lesson_week_count_name"];
    }
    if(form_data["lesson_week_name"]) form_data["schedule_name"] += " "+form_data["lesson_week_name"];


    var _sd = $('input[name=enable_start_date]').val();
    var _ed = $('input[name=enable_end_date]').val();
    form_data["enable_dulation"] = "-";
    if(!util.isEmpty(_sd) && !util.isEmpty(_ed)) form_data["enable_dulation"] = _sd+"～"+_ed;
    else if(!util.isEmpty(_sd)) form_data["enable_dulation"] = _sd+"～";
    else if(!util.isEmpty(_ed)) form_data["enable_dulation"] = "～"+_ed;
    console.log(form_data);
    return form_data;
  }
});
</script>
