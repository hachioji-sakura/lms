<div id="calendars_entry" class="direct-chat-msg">
  @include('calendars.create_form')
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
    <div class="carousel slide" data-ride="carousel" data-interval="false">
      <div class="carousel-inner">
        <div class="carousel-item active">
          @yield('first_form')
          <div class="row">
            @if($item->work==9)
            <div class="col-12 mb-1">
              <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="calendars_entry">
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
        @if($item->work!=9)
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
              <a href="javascript:void(0);" role="button" class="btn-next btn btn-primary btn-block float-left mr-1 btn-confirm">
                <i class="fa fa-file-alt mr-1"></i>
                {{__('labels.confirm_button')}}
              </a>
            </div>
          </div>
        </div>
        @endif
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
                <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="calendars_entry"
                  @if(isset($_edit) && $_edit==true)
                  confirm="{{__('messages.confirm_update')}}">
                    {{__('labels.update_button')}}
                  @else
                  confirm="{{__('messages.confirm_add')}}">
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
  base.pageSettinged("calendars_entry", form_data);
  $('#calendars_entry').carousel({ interval : false});
  lesson_change();

  //次へ
  $('.carousel-item .btn-next').on('click', function(e){

    if($(this).hasClass('btn-confirm')){
      @if($item['exchanged_calendar_id'] > 0)
      if($("input[name='course_minutes']").length > 0 ){
        get_exchange_calendar();
      }
      @endif
    }

    var form_data = front.getFormValue('calendars_entry');
    if(front.validateFormValue('calendars_entry .carousel-item.active')){
      $('body, html').scrollTop(0);
      $('#calendars_entry').carousel('next');
      $('#calendars_entry').carousel({ interval : false});
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
    $('#calendars_entry').carousel('prev');
    $('#calendars_entry').carousel({ interval : false});
  });
  //確認画面用のパラメータ調整
  function form_data_adjust(form_data){
    console.log("form_data_adjust:"+form_data);
    var _names = ["lesson", "lesson_place", "kids_lesson", "english_talk_lesson"];
    $.each(_names, function(index, value) {
      if(util.isEmpty(form_data[value+"_name"])){
        form_data[value+"_name"] = "";
        if(form_data[value+'[]']){
          $("input[name='"+value+'[]'+"']:checked").each(function() {
            form_data[value+"_name"] += $(this).parent().parent().text().trim()+'<br>';
          });
        }
      }
    });

    var _t = $('input[name=teacher_id]').attr("alt");
    form_data["teacher_name"] = _t;

    var _d = $('input[name=start_date]').val();
    var _sh = $('select[name=start_hours] option:selected').val();
    var _sm = $('select[name=start_minutes] option:selected').val();
    if(_d && _sh && _sm) form_data["start_time"] = _d+" "+_sh+":"+_sm;

    var _eh = $('select[name=end_hours] option:selected').val();
    var _em = $('select[name=end_minutes] option:selected').val();
    if(_eh && _em){
      form_data["work_time"] = _d+" "+_sh+":"+_sm+"-"+_eh+":"+_em;
    }

    var _snames = "";
    if($('select[name="student_id[]"] option:selected').length > 0){
      $('select[name="student_id[]"] option:selected').each(function(){
        var _sname = $(this).text().trim();
        _snames+=_sname+"<br>";
      });
      form_data["student_name"] = _snames;
    }
    var _names = ["place_floor_id", "student_group", "work"];
    $.each(_names, function(index, value) {
      if(util.isEmpty(form_data[value+"_name"])){
        form_data[value+"_name"] = "";
        if(form_data[value]){
          var _name = $('select[name='+value+'] option:selected').text().trim();
          form_data[value+"_name"] = _name;
        }
        if(value=='place_floor_id'){
          if($('input[name="is_online"]').val()=='true'){
            form_data[value+"_name"] += '/ <i class="fa fa-globe"></i>{{__('labels.online')}}';
          }
        }
      }
    });

    _names = ["course_minutes", "course_type"];
    $.each(_names, function(index, value) {
      if(util.isEmpty(form_data[value+"_name"])){
        form_data[value+"_name"] = "";
        if(form_data[value]){
          $("input[name='"+value+"']:checked").each(function() {
            form_data[value+"_name"] = $(this).parent().parent().text().trim()+'<br>';
          });
        }
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
    //get_conflict_calendar(form_data);
    return form_data;
  }
});
</script>
