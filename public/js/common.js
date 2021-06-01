$(function(){
  $('a[href^="#"][scroll]').click(function() {
    var href= $(this).attr("href");
    var scroll= $(this).attr("scroll");
    var target = $(href == "#" || href == "" ? 'html' : href);
    if(target.length>0){
      var position = target.offset().top;
      $('body,html').animate({scrollTop:position}, scroll, 'swing', function(){
      });
    }
  });
  $('.toggle-btn').click(function() {
    //指定したidを閉じたり開いたり
    var id = $(this).attr("target");
    var _btn = $(this);
    $('#'+id).slideToggle("fast", function(){
      if($(this).is(":hidden")){
        $(".toggle-btn-open", _btn).show();
        $(".toggle-btn-close", _btn).hide();
      }
      else {
        $(".toggle-btn-open", _btn).hide();
        $(".toggle-btn-close", _btn).show();
      }
    });
  });

  var _timer = null;
  $(window).on('scroll',function(){
    var heroBottom = $('.main-header').height();
    $('.main-header').css('top', 0);
    if($(window).scrollTop() > heroBottom){
      if(_timer!=null) clearTimeout(_timer);
      _timer = setTimeout(header_scroll, 300);
    }
  });
  function header_scroll(){
    var heroBottom = $('.main-header').height();
    if($(window).scrollTop() > heroBottom){
      $('.main-header').animate({'top' : $(window).scrollTop()},'fast', function(){
        if($(window).scrollTop() > heroBottom){
          $('.main-header').css('top', $(window).scrollTop());
        }
      });
    }
    else {
      $('.main-header').css('top', 0);
    }
  }
  $(window).trigger('scroll');

  //キーワード検索
  $("#search_button").on("click", function(e){
    var _search_word = $("input[name=search_word]").val();
    _search_word = _search_word.replace_all('+', '%2B');
    _search_word = _search_word.replace_all('　', ' ');
    if(!util.isEmpty(_search_word)){
      location.href = service.setQueryParam({"search_word" : _search_word});
    }
    else {
      location.href = service.setQueryParam({"search_word" : ""});
    }
  });
  $("input[name='search_word'][accesskey='keyword_search']").on("keypress", function(e){
    if(e.keyCode==13){
      //検索入力～Enterで、検索ボタン押下
      $("#search_button").click();
    }
  });
  $("input[name='search_word'][accesskey='keyword_search']").on("focusin", function(e){
    $(this).animate({width:"260px"},500,"easeInOutExpo");
  });
  $("input[name='search_word'][accesskey='keyword_search']").on("focusout", function(e){
    $(this).animate({width:"140px"},500,"easeInOutExpo");
  });
  //ダイアログでサブページを開く場合、
  $("a[page_url][page_title][page_form=dialog]").on("click", function(e){
    console.log($(this).attr('page_title'));
    base.showPage("dialog", "subDialog", $(this).attr("page_title"), $(this).attr("page_url"));
  });
  //フッターから出てくるタイプのフォーム
  $("a[page_url][page_title][page_form='footer_form'], a.nav-link[page_url][page_title][page_form='footer_form']").on("click", function(e){
    base.showPage("footer", "footer_form", $(this).attr("page_title"), $(this).attr("page_url"));
  });
  // #で始まるアンカーをクリックした場合に処理
  $("a[href^='#'][scroll]").on("click", function(){
    var speed = 400; // ミリ秒
	  // アンカーの値取得
	  var href= jQuery(this).attr("href");
	  // 移動先を取得
	  var target = jQuery(href == "#" || href == "" ? 'html' : href);
	  // 移動先を数値で取得
    if(target.length>0){
      var h = target.height();
  	  var position = target.offset().top - (h/2);
  	  // スムーススクロール
  	  jQuery('body,html').animate({scrollTop:position}, speed, 'swing');
    }
    return false;
  });
  //1時間経過したら再読み込み
  setTimeout('window.location.reload();', 3600000);
});
function birth_day_form_change(){
  $("input.birth_day[type=hidden]").each(function(index,element){
    var _name = $(this).attr("name");
    var year = $('select[name="'+_name+'_year"]').val();
    var month = $('select[name="'+_name+'_month"]').val();
    var day = $('select[name="'+_name+'_day"]').val();
    var date = year+'/'+month+'/'+day;
    $(this).val(date);
    if($('*[name="grade"]').length > 0){
      var grade = get_grade(year,month,day);
      $('*[name="grade"]').val(grade);
      $('*[name="grade"]').change();
    }
  });
}
//Models/Student::default_grade
function get_grade(year, month, day){
  var age = util.getAge(year, month, day);
  var now = util.nowDate().replace_all('/', '');
  var strdate = ( year+ '' + util.leftPadZero(month, 2) + '' + util.leftPadZero(day, 2));
  //各月日を求める
  var b_y = (strdate.substr(0,4))|0;
  var b_m = (strdate.substr(4,8))|0;
  var n_y = (now.substr(0,4))|0;
  var n_m = (now.substr(4,8))|0;
  var m= 0;
  if (n_m < 400) { //前学期
      m = 1;
  }
  if(b_m < 402) { //早生まれ
      n_y++;
  }
  //学年の計算
  var grade_code = n_y - b_y - m;
  //結果を返す
  if(grade_code < 4){
    return 'k1';
  }
  var grade_index = grade_code-4;
  var i = 0;
  for(var key in config_grade){
    if(i==grade_index) return key;
    i++;
  }
  return 'adult';
}
function is_school(grade_name){
  var ret = false;
  if(grade_name.substring(0,1)=="幼"){
    ret = true;
  }
  else if(grade_name.substring(0,1)=="高"){
    ret = true;
  }
  else if(grade_name.substring(0,1)=="中"){
    ret = true;
  }
  else if(grade_name.substring(0,1)=="小"){
    ret = true;
  }
  else if(grade_name.substring(0,1)=="大"){
    ret = true;
  }
  return ret;
}
function get_subject_grade(grade_name){
  var _grade_name = "";
  if(grade_name.substring(0,1)=="高"){
    _grade_name = "高校";
  }
  else if(grade_name.substring(0,1)=="中"){
    _grade_name = "中学";
  }
  else if(grade_name.substring(0,1)=="小"){
    _grade_name = "小学";
  }
  else if(grade_name.substring(0,1)=="大"){
    _grade_name = "高校";
  }
  else if(grade_name.substring(0,1)=="成"){
    _grade_name = "高校";
  }
  else if(grade_name.substring(0,1)=="幼"){
    _grade_name = "小学";
  }
  else if(grade_name.substring(0,1)=="年"){
    _grade_name = "小学";
  }
  return _grade_name;
}
function subject_onload(){
  console.log("subject_onload");
  $(".grade-subject").hide();
  if($('select.grade').length > 0){
    $('select.grade').each(function(index, element){
      var _name = $(this).attr('name');
      var grade_name = $('select[name="'+_name+'"] option:selected').text().trim();
      if(is_school(grade_name)){
        $("."+_name+"_school_name_form").collapse("show");
        $("."+_name+"_school_name_confirm").collapse("show");
      }
      else {
        $("."+_name+"_school_name_form").collapse("hide");
        $("."+_name+"_school_name_confirm").collapse("hide");
      }
      var subject_grade = get_subject_grade(grade_name);
      $(".grade-subject[alt='"+subject_grade+"']").show();
    });
  }
  else {
    if($('input.grade[type=hidden]').length > 0){
      $('input.grade[type=hidden]').each(function(index, element){
        var _name = $(this).attr('name');
        var grade_name = $('input[name="'+_name+'_name"]').val();
        if(is_school(grade_name)){
          $("."+_name+"_school_name_form").collapse("show");
          $("."+_name+"_school_name_confirm").collapse("show");
        }
        else {
          $("."+_name+"_school_name_form").collapse("hide");
          $("."+_name+"_school_name_confirm").collapse("hide");
        }
        var subject_grade = get_subject_grade(grade_name);
        $(".grade-subject[alt='"+subject_grade+"']").show();
      });
    }
  }
}
function subject_validate(){
  var _is_scceuss = false;
  var is_school = $('input[type="checkbox"][name="lesson[]"][value="1"]').prop("checked");
  if(!is_school) return true;
  if( $("input.subject_level[type='radio']", $(".carousel-item.active")).length > 0){
    $("input.subject_level[type='radio'][value!=1]:checked", $(".carousel-item.active")).each(function(index, value){
      var val = $(this).val();
      if(val!=1){
        _is_scceuss = true;
      }
    });
    if(!_is_scceuss){
      front.showValidateError('#subject_table', '希望科目を１つ以上選択してください');
    }
  }
  else {
    return true;
  }
  return _is_scceuss;
}
function lesson_checkbox_change(obj){
  var name = $(obj).attr('name');
  console.log("lesson_checkbox_change");
  var check_lesson = get_lesson_check(name);
  if(check_lesson["is_school"]){
    $(".subject_form").show();
    $(".subject_confirm").show();
  }
  else {
    $(".subject_form").hide();
    $(".subject_confirm").hide();
  }
  if(check_lesson["is_english"]){
    $(".english_talk_form").show();
    $(".english_talk_form input").show();
    $(".english_talk_form select").show();
    $(".english_talk_confirm").show();
  }
  else {
    $(".english_talk_form").hide();
    $(".english_talk_form input").hide();
    $(".english_talk_form select").hide();
    $(".english_talk_confirm").hide();
  }
  if(check_lesson["is_piano"]){
    $(".piano_form").show();
    $(".piano_form input").show();
    $(".piano_form select").show();
    $(".piano_confirm").show();
  }
  else {
    $(".piano_form").hide();
    $(".piano_form input").hide();
    $(".piano_form select").hide();
    $(".piano_confirm").hide();
  }
  if(check_lesson["is_kids_lesson"]){
    $(".kids_lesson_form").show();
    $(".kids_lesson_form input").show();
    $(".kids_lesson_form select").show();
    $(".kids_lesson_confirm").show();
  }
  else {
    $(".kids_lesson_form").hide();
    $(".kids_lesson_form input").hide();
    $(".kids_lesson_form select").hide();
    $(".kids_lesson_confirm").hide();
  }
  lesson_place_filter(name);
  course_minutes_filter(name);
  //grade_select_change();
}
function lesson_place_filter(name){
  var check_lesson = get_lesson_check(name);
  $("label.lesson_place").show();
  $("label.lesson_place:contains('ダットッチ校')").hide();
  if(!check_lesson["is_school"] && !check_lesson["is_english"] &&
      !check_lesson["is_piano"] && !check_lesson["is_kids_lesson"]){
      //lessonを選択していないならば、無処理
      return ;
  }
  if(!check_lesson["is_school"] && !check_lesson["is_english"]){
    //ピアノ＝子安、
    $("label.lesson_place:contains('三鷹校')").hide();
    $("label.lesson_place:contains('八王子北口校')").hide();
    $("label.lesson_place:contains('国立校')").hide();
    $("label.lesson_place:contains('日野市豊田校')").hide();
    $("label.lesson_place:contains('アローレ校')").hide();
    if(!check_lesson["is_kids_lesson"]){
      $("label.lesson_place:contains('八王子南口校')").hide();
    }
  }
  else if(!check_lesson["is_school"] && check_lesson["is_english"]){
    $("label.lesson_place:contains('アローレ校')").hide();
  }
}
function course_minutes_filter(name){
  console.log("course_minutes_filter("+name+")");
  var check_lesson = get_lesson_check(name);
  console.log(check_lesson);
  var exchanged_calendar_id = $("input[name='exchanged_calendar_id']").val();
  $("label.course_minutes").show();
  if(exchanged_calendar_id && exchanged_calendar_id>0){
    //振替の場合は分割振替などがあるので、フィルタはなし
    return ;
  }
  var is_calendar_settings = $('input[name="is_calendar_settings"]').val()|0;
  var trial_id = $('input[name="trial_id"]').val()|0;
  if(trial_id > 0 && is_calendar_settings== 0){
    $("label.course_minutes:contains('９０分')").hide();
    $("label.course_minutes:contains('１２０分')").hide();
  }
  else {
    if(!check_lesson["is_school"]){
      //塾以外＝90分、120分なし
      $("label.course_minutes:contains('９０分')").hide();
      $("label.course_minutes:contains('１２０分')").hide();
    }
    if(!check_lesson["is_piano"] && !check_lesson["is_english"] && !check_lesson["is_kids_lesson"]){
      //習い事がない
      $("label.course_minutes:contains('３０分')").hide();
    }
  }
}
function lesson_change(){
  var lesson = ($('input[name=lesson]:checked').val())|0;
  if(lesson==0){
    lesson = ($('input[name=lesson]').val())|0;
  }
  $(".charge_subject").hide();
  $("#course_type_form .form-check").hide();
  $("#course_type_form_single").show();
  $("#course_type_form_family").show();
  $(".charge_subject_"+lesson).show();
  console.log("lesson_change:"+lesson);
  switch(lesson){
    case 2:
    case 4:
      $("#course_type_form_group").show();
      break;
  }
  $(".lesson_selected").collapse('show');
  course_type_change();
  course_minutes_filter('lesson')
}
function course_type_change(){
  var course_type = $('input[type="radio"][name="course_type"]:checked').val();
  if(!course_type){
    course_type = $('input[type="hidden"][name="course_type"]').val();
  }
  if(!course_type){
    return false;
  }
  console.log('course_type_change:'+course_type);
  if($("select[name='student_id[]']").length>0){
    var student_id_form = $("select[name='student_id[]']");
    var _width = student_id_form.attr("width");
    student_id_form.select2('destroy');
    student_id_form.removeAttr("multiple");
    if(course_type!=="single" && student_id_form.attr('multiple')!='multiple'){
      //グループ or ファミリーの場合
      get_student_group();
      student_id_form.attr("multiple", "multiple");
      console.log('course_type_change:'+course_type);
      $(".course_type_selected").collapse('show');
    }
    else {
      $(".course_type_selected").collapse('hide');
    }
    student_id_form.select2({
      width: _width,
      placeholder: '選択してください',
    });
    student_id_form.val(-1).trigger('change');
  }
}
function get_lesson_check(name){
  var is_school = false;
  var is_english = false;
  var is_piano = false;
  var is_kids_lesson = false;
  if($('input[name="'+name+'"][type="checkbox"],input[name="'+name+'"][type="radio"]').length > 0){
    is_school = $('input[name="'+name+'"][value="1"]').prop("checked");
    is_english = $('input[name="'+name+'"][value="2"]').prop("checked");
    is_piano = $('input[name="'+name+'"][value="3"]').prop("checked");
    is_kids_lesson = $('input[name="'+name+'"][value="4"]').prop("checked");
  }
  else if($('input[name="'+name+'"][type="hidden"]').length > 0){
    var _lesson = $('input[name="'+name+'"][type="hidden"]').val();
    if(_lesson == 1) is_school = true;
    else if(_lesson == 2) is_english = true;
    else if(_lesson == 3) is_piano = true;
    else if(_lesson == 4) is_kids_lesson = true;
  }
  return {
    'is_school' : is_school,
    'is_english' : is_english,
    'is_piano' : is_piano,
    'is_kids_lesson' : is_kids_lesson
  };
}

function select_student_change(){
  var options = {};
  console.log("select_student_change");
  var selecter = "select[name='student_id[]'] option:selected";
  if($(selecter).length < 1){
    selecter = "*[name='student_id[]']";
  }
  //選択した生徒の学年に応じて、塾の科目を絞り込む
  var _is_select_student = false;
  $(selecter).each(function(){
    var val = $(this).val();
    var grade = $(this).attr("grade");
    var grade_code = "";
    if(!util.isEmpty(grade)){
      grade_code = grade.substr(0,1);
      if(grade=='university') grade_code='h';
      if(grade=='adult') grade_code='h';
      if(grade.match(/k/))  grade_code = 'e';
    }
    $("select[name='__charge_subject[]'] option[grade='"+grade_code+"']").each(function(){
      options[$(this).val()] = $(this).text();
    });
    console.log(val+":"+grade_code);
    if(val|0 > 0){
      _is_select_student = true;
    }
  });
  var _options = [];
  var _option_html = "";
  $.each(options, function(i, v){
    _options.push({'id':i, 'text':v});
    _option_html+='<option value="'+i+'">'+v+'</option>';
  });
  if($("select[name='charge_subject[]']").length > 0 && $("select[name='__charge_subject[]']").length > 0){
    var charge_subject_form = $("select[name='charge_subject[]']");
    var _width = charge_subject_form.attr("width");
    charge_subject_form.select2('destroy');
    var selected =  $("select[name='__charge_subject[]']").val();

    //charge_subject_form.empty();
    charge_subject_form.html(_option_html);
    $("select[name='charge_subject[]']").val(selected);
    charge_subject_form.select2({
      width: _width,
      placeholder: '選択',
    });
  }
  if(_is_select_student){
    var course_type = $('input[type="radio"][name="course_type"]:checked').val();
    if($('input[name=exchanged_calendar_datetime]').length > 0){
      if(course_type=="single"){
        //マンツーの場合振替対象を取得
        $('input[name=exchanged_calendar_datetime]').val('');
        $('input[name=exchanged_calendar_id]').val('');
      }
    }
  }
}
function all_checked_change(obj){
  var _val = $(obj).val();
  var _checked = $(obj).prop('checked');
  var _accesskey = $(obj).attr('accesskey');
  if(!util.isEmpty(_accesskey)){
    if(_checked){
      $('*[name="'+_accesskey+'"]').each(function(i, e){
        $(this).iCheck('check');
      });
    }
    else{
      $('*[name="'+_accesskey+'"]').each(function(i, e){
        $(this).iCheck('uncheck');
      });
    }
  }
}
function is_checked_exist(group_id, target_name){
  var _is_checked = false;
  $('*[name="'+target_name+'"]').each(function(){
    var f = $(this).prop('checked');
    if(f) _is_checked = true;
  });
  if(_is_checked==false){
    front.showValidateError('#'+group_id, '対象が選択されていません');
  }
  return _is_checked;
}
