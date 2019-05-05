<?php
  $key_names = [0=>"", 1=>"charge_subject", 2=>"english_talk_lesson", 3=>"piano_lesson", 4=>"kids_lesson"];
  $key_name = $key_names[$select_lesson];
 ?>
 <div class="col-6 mt-2">
   <div class="form-group">
     <label for="{{$key_name}}" class="w-100">
       担当科目
       <span class="right badge badge-danger ml-1">必須</span>
     </label>
     <select name="{{$key_name}}[]" class="form-control select2" placeholder="担当科目" required="true" multiple="multiple">
       <option value="">(選択してください)</option>
       @foreach($candidate_teacher->enable_subject as $index=>$subject)
         <option value="{{$subject['subject_key']}}"
         @if(isset($calendar))
           @foreach($calendar->get_tags('charge_subject') as $index => $tag)
             @if($tag->tag_value===$subject['subject_key'])
               selected
             @endif
           @endforeach
         @elseif($item->has_tag($subject['subject_key'].'_level')==true)
          selected
         @endif
         >{{$subject['subject_name']}}</option>
       @endforeach
     </select>
   </div>
 </div>
