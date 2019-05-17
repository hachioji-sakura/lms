 <div class="col-12 mt-2">
   <label for="charge_subject" class="w-100">
     担当科目{{$teacher->user->has_tag('lesson', '3')}}
     <span class="right badge badge-danger ml-1">必須</span>
   </label>
     @if($teacher->user->has_tag('lesson', '1')===true)
     <div class="form-group w-100 charge_subject_1 charge_subject">
       <select name="charge_subject[]" class="form-control select2" width=100% placeholder="担当科目" required="true" multiple="multiple" >
       </select>
       <select name="__charge_subject[]" class="hide" >
         <option value="">(選択)</option>
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
       <select name="english_talk_lesson[]" class="form-control select2" width=100% placeholder="担当科目" required="true" multiple="multiple" >
         <option value="">(選択)</option>
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
       <select name="kids_lesson[]" class="form-control select2" width=100% placeholder="担当科目" required="true" multiple="multiple" >
         <option value="">(選択)</option>
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
     @if($teacher->user->has_tag('lesson', '3')===true)
     <div class="form-group w-100 charge_subject_2 charge_subject">
       ピアノレッスン
       <input type="hidden" name="piano_lesson" value="piano" >
     </div>
     @endif
 </div>
