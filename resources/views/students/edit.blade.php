@include('parents.create_form')
<div id="students_register" class="direct-chat-msg">
  <form method="POST"  action="/students/{{$item->id}}">
    @csrf
    @method('PUT')
    <div id="students_edit" class="carousel slide" data-ride="carousel" data-interval=false>
      <div class="carousel-inner">
        <div class="carousel-item active">
          @yield('student_form')
          <div class="row">
            <div class="col-12 mb-1">
              <a href="/" role="button" class="btn btn-secondary btn-block float-left mr-1">
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
          @yield('subject_form')
          <div class="row">
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-prev btn btn-secondary btn-block float-left mr-1">
                <i class="fa fa-arrow-circle-left mr-1"></i>
                戻る
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
          @yield('lesson_week_form')
          <div class="row">
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-prev btn btn-secondary btn-block float-left mr-1">
                <i class="fa fa-arrow-circle-left mr-1"></i>
                戻る
              </a>
            </div>
            <div class="col-12 mb-1">
                <a href="javascript:void(0);" role="button" class="btn-next btn btn-primary btn-block float-left mr-1">
                  <i class="fa fa-check-circle mr-1"></i>
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
            @if(isset($user->role))
            <div class="col-12 mb-1">
              <a href="/" role="button" class="btn btn-secondary btn-block float-left mr-1">
                <i class="fa fa-times-circle mr-1"></i>
                キャンセル
              </a>
            </div>
            @endif
            <div class="col-12 mb-1">
                <button type="submit" class="btn btn-primary btn-block" accesskey="students_create">
                  <i class="fa fa-edit mr-1"></i>
                    更新する
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
  var form_data = util.getLocalData('students_edit');
  base.pageSettinged("students_edit", form_data);

  //submit
  $("button[type='submit']").on('click', function(e){
    e.preventDefault();
    if(front.validateFormValue('students_edit .carousel-item.active')){
      $("form").submit();
    }
  });

  //次へ
  $('.carousel-item .btn-next').on('click', function(e){
    if(front.validateFormValue('students_edit .carousel-item.active')){
      var form_data = front.getFormValue('students_edit');
      util.setLocalData('students_edit', form_data);
      base.pageSettinged("confirm_form", form_data_adjust(form_data));
      $('#students_edit').carousel('next');
    }
  });
  //戻る
  $('.carousel-item .btn-prev').on('click', function(e){
    $('#students_edit').carousel('prev');
  });
  //確認画面用のパラメータ調整
  function form_data_adjust(form_data){
    form_data["email"] = $("input[name=email]").val();
    if(form_data["grade"]){
      form_data["grade_name"] = $('select[name=grade] option:selected').text().trim();
    }
    var _names = ["lesson", "lesson_place", "howto", "kids_lesson"];
    $.each(_names, function(index, value) {
      form_data[value+"_name"] = "";
      if(form_data[value+'[]']){
        $("input[name='"+value+'[]'+"']:checked").each(function() {
          form_data[value+"_name"] += $(this).parent().parent().text().trim()+'<br>';
        });
      }
    });
    _names = ["english_teacher", "piano_level", "course_type", "course_minutes", "gender"];
    $.each(_names, function(index, value) {
      form_data[value+"_name"] = "";
      if(form_data[value]){
        $("input[name='"+value+"']:checked").each(function() {
          form_data[value+"_name"] += $(this).parent().parent().text().trim()+'<br>';
        });
      }
    });
    form_data["subject_level_name"] = "";
    $("input.subject_level[type='radio'][value!=1]:checked").each(function(index, value){
      var val = $(this).val();
      var name = $(this).attr("name");
      name = name.replace('[]', '');
      form_data[name+"_"+val+"_name"] = "〇";
    });
    return form_data;
  }
});
</script>
