 <div class="col-12 mt-2 schedule_type schedule_type_class">
   <label for="charge_subject" class="w-100">
     {{__('labels.charge_subject')}}
     <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
   </label>
     @if($teacher->user->has_tag('lesson', '1')===true)
       <div class="form-group w-100 charge_subject_1 charge_subject">
         <select name="charge_subject[]" class="form-control select2" width=100% placeholder="{{__('labels.charge_subject')}}" required="true" multiple="multiple" >
         </select>
         <select name="__charge_subject[]" class="hide" multiple="multiple" >
           <option value="">{{__('labels.selectable')}}</option>
           @foreach($teacher->get_subject(1) as $index=>$subject)
             <option
             grade="{{$subject['grade']}}"
             value="{{$subject['subject_key']}}"
             @if(isset($_edit) && $_edit==true && $item->has_tag('charge_subject', $subject['subject_key']))
             selected
             @endif
             >{{$subject['subject_name']}}</option>
           @endforeach
         </select>
       </div>
     @endif
     @if($teacher->user->has_tag('lesson', '2')===true)
     <div class="form-group w-100 charge_subject_2 charge_subject">
       <select name="english_talk_lesson[]" class="form-control select2" width=100% placeholder="{{__('labels.charge_subject')}}" required="true" multiple="multiple" >
         <option value="">{{__('labels.selectable')}}</option>
         @foreach($teacher->get_subject(2) as $index=>$subject)
           <option value="{{$subject['subject_key']}}"
           @if(isset($_edit) && $_edit==true && $item->has_tag('english_talk_lesson', $subject['subject_key']))
           selected
           @endif
           >{{$subject['subject_name']}}</option>
         @endforeach
       </select>
     </div>
     @endif
     @if($teacher->user->has_tag('lesson', '4')===true)
     <div class="form-group w-100 charge_subject_4 charge_subject">
       <select name="kids_lesson[]" class="form-control select2" width=100% placeholder="{{__('labels.charge_subject')}}" required="true" multiple="multiple" >
         <option value="">{{__('labels.selectable')}}</option>
         @foreach($teacher->get_subject(4) as $index=>$subject)
           <option value="{{$subject['subject_key']}}"
           @if(isset($_edit) && $_edit==true && $item->has_tag('kids_lesson', $subject['subject_key']))
           selected
           @endif
           >{{$subject['subject_name']}}</option>
         @endforeach
       </select>
      </div>
     @endif
     @if($_edit==false && $teacher->user->has_tag('lesson', '3')===true)
     <div class="form-group w-100 charge_subject_2 charge_subject">
       ピアノレッスン
       <input type="hidden" name="piano_lesson" value="piano" >
     </div>
     @endif
 </div>
 <script>
 $(function(){
   //編集時は、生徒が決定しているので科目の初期表示をする
   select_student_change();
 });
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
       placeholder: '{{__('labels.selectable')}}',
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
 </script>
