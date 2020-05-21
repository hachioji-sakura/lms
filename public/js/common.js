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
  if(!check_lesson["is_school"] && !check_lesson["is_english"]){
    //ピアノ＝子安、
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