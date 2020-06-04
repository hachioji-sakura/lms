<script>
$(function(){
  @if($_edit==true)
  var form_data = null;
  @else
  var form_data = util.getLocalData('trials_entry');
  @endif
  base.pageSettinged("trials_entry", form_data);
  subject_onload();

  $('#trials_entry').carousel({ interval : false});
  if(form_data && !util.isEmpty(form_data['student2_name_last'])){
    $('.student2').collapse('show');
  }
  if(form_data && !util.isEmpty(form_data['student3_name_last'])){
    $('.student3').collapse('show');
  }
  $("input[name='lesson[]']").change();
  //submit
  $("button.btn-submit").on('click', function(e){
    e.preventDefault();
    if(front.validateFormValue('trials_entry .carousel-item.active')){
      $("form").submit();
    }
  });

  //次へ
  $('.carousel-item .btn-next').on('click', function(e){
    var form_data = front.getFormValue('trials_entry');
    if(front.validateFormValue('trials_entry .carousel-item.active')){
      @if($_edit==false)
      util.setLocalData('trials_entry', form_data);
      @endif
      $('body, html').scrollTop(0);
      $('#trials_entry').carousel('next');
      $('#trials_entry').carousel({ interval : false});
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
    $('#trials_entry').carousel('prev');
    $('#trials_entry').carousel({ interval : false});
  });
  //確認画面用のパラメータ調整
  function form_data_adjust(form_data){
    console.log('form_data_adjust');
    form_data["email"] = $("input[name=email]").val();
    for(var i=1;i<4;i++){
      if(form_data["trial_date"+i] && form_data["trial_start_time"+i] && form_data["trial_end_time"+i]){
        var trial_start = $('select[name=trial_start_time'+i+'] option:selected').text().trim();
        var trial_end = $('select[name=trial_end_time'+i+'] option:selected').text().trim();
        form_data["trial_date_time"+i] = util.dateformat(form_data["trial_date"+i], '%m月%d日(%w)')+'<br>'+trial_start+"時 ～ "+trial_end+"時";
      }
    }
    var _names = ["lesson", "lesson_place", "howto", "kids_lesson", "english_talk_lesson"];
    $.each(_names, function(index, value) {
      form_data[value+"_name"] = "";
      if(form_data[value+'[]']){
        $("input[name='"+value+'[]'+"']:checked").each(function() {
          var t = $(this).parent().parent().text().trim();
          if(!util.isEmpty(t)){
            console.log(t);
            t = t.replace_all('[MAP]', '');
            t = t.replace_all('<br>', '');
            form_data[value+"_name"] += t.trim()+'<br>';
          }
        });
      }
    });

    var _names = ["grade", "student2_grade", "student3_grade"];
    $.each(_names, function(index, value) {
      if(form_data[value]){
        var _name = $('select[name='+value+'] option:selected').text().trim();
        form_data[value+"_name"] = _name;
      }
    });

    _names = ["english_teacher", "piano_level",
              "english_talk_course_type", "kids_lesson_course_type",
              "course_minutes", "lesson_week_count",
              "gender",
              "student2_gender",
              "student3_gender"
            ];
    $.each(_names, function(index, value) {
      form_data[value+"_name"] = "";
      if(form_data[value]){
        $("input[name='"+value+"']:checked").each(function() {
          form_data[value+"_name"] += $(this).parent().parent().text().trim()+'<br>';
        });
      }
    });
    $("input.week_time[type='checkbox'][value!='disabled']:checked").each(function(index, value){
      var val = $(this).val();
      var name = $(this).attr("name");
      name = name.replace('[]', '');
      form_data[name+"_"+val+"_name"] = "〇";
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
