@include('students.create_form')
<div id="students_register" class="direct-chat-msg">
  <form method="POST"  action="/students/{{$item->id}}">
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    @method('PUT')
    <div id="students_edit" class="carousel slide" data-ride="carousel" data-interval="false">
      <div class="carousel-inner">
        <div class="carousel-item active">
          <div class="row">
            <div class="col-12">
              <h5 class="bg-info p-1 pl-2 mb-4">
                <i class="fa fa-user-graduate mr-1"></i>
                生徒様情報
              </h5>
            </div>
            @component('students.forms.name', ['_edit'=>$_edit, 'item' => $student, 'prefix' => '']) @endcomponent
            @component('students.forms.kana', ['_edit'=>$_edit, 'item' => $student, 'prefix' => '']) @endcomponent
            <div class="col-12">
              @component('components.select_birthday', ['_edit'=>$_edit, 'item' => $student,'prefix' => ''])
              @endcomponent
            </div>
            <div class="col-12">
              @component('components.select_gender', ['_edit'=>$_edit, 'item' => $student,'prefix' => ''])
              @endcomponent
            </div>
            @component('students.forms.school', ['_edit'=>$_edit, 'item' => $student, 'attributes' => $attributes,'prefix' => '']) @endcomponent
          </div>
          <div class="row">
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-prev btn btn-secondary btn-block float-left mr-1">
                <i class="fa fa-arrow-circle-left mr-1"></i>
                戻る
              </a>
            </div>
            <div class="col-12 mb-1">
                <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="students_create">
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
  grade_select_change();
  base.pageSettinged("students_edit", []);
  $('#students_edit').carousel({ interval : false});

  //submit
  $("button.btn-submit").on('click', function(e){
    e.preventDefault();
    if(front.validateFormValue('students_edit .carousel-item.active')){
      $("form").submit();
    }
  });

  //次へ
  $('.carousel-item .btn-next').on('click', function(e){
    if(front.validateFormValue('students_edit .carousel-item.active')){
      var form_data = front.getFormValue('students_edit');
      base.pageSettinged("confirm_form", form_data_adjust(form_data));
      $('#students_edit').carousel('next');
      $('#students_edit').carousel({ interval : false});
    }
  });
  //戻る
  $('.carousel-item .btn-prev').on('click', function(e){
    $('#students_edit').carousel('prev');
    $('#students_edit').carousel({ interval : false});
  });
  //確認画面用のパラメータ調整
  function form_data_adjust(form_data){
    form_data["email"] = $("input[name=email]").val();
    if(form_data["grade"]){
      form_data["grade_name"] = $('select[name=grade] option:selected').text().trim();
    }
    var _names = ["lesson", "lesson_place", "howto", "kids_lesson", "english_talk_lesson"];
    $.each(_names, function(index, value) {
      form_data[value+"_name"] = "";
      if(form_data[value+'[]']){
        $("input[name='"+value+'[]'+"']:checked").each(function() {
          var t = $(this).parent().parent().text().trim();
          t = t.replace_all('[MAP]', '');
          form_data[value+"_name"] += t+'<br>';
        });
      }
    });

    return form_data;
  }
});
</script>
