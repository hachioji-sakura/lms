@if($select_lesson==1)
<div class="col-12 mt-2">
  <div class="form-group">
    <label for="charge_subject" class="w-100">
      担当科目
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    <select name="charge_subject[]" class="form-control select2" placeholder="担当科目" required="true" multiple="multiple">
      <option value="">(選択してください)</option>
      @foreach($candidate_teachers->enable_subject as $index=>$subject)
        <option value="{{$subject['subject_key']}}">{{$subject['subject_name']}}</option>
      @endforeach
    </select>
  </div>
</div>
@elseif($select_lesson==2)
<div class="col-12 mt-2">
  <div class="form-group">
    <label for="english_talk_lesson" class="w-100">
      担当レッスン
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    <select name="english_talk_lesson[]" class="form-control select2" placeholder="担当科目" required="true" multiple="multiple">
      <option value="">(選択してください)</option>
      @foreach($candidate_teachers->enable_subject as $index=>$subject)
        <option value="{{$subject['subject_key']}}">{{$subject['subject_name']}}</option>
      @endforeach
    </select>
  </div>
</div>
@elseif($select_lesson==3)
<div class="col-12 mt-2">
  <div class="form-group">
    <label for="piano_lesson" class="w-100">
      担当レッスン
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    <select name="piano_lesson[]" class="form-control select2" placeholder="担当科目" required="true" multiple="multiple">
      <option value="">(選択してください)</option>
      @foreach($candidate_teachers->enable_subject as $index=>$subject)
        <option value="{{$subject['subject_key']}}">{{$subject['subject_name']}}</option>
      @endforeach
    </select>
  </div>
</div>
@elseif($select_lesson==4)
<div class="col-12 mt-2">
  <div class="form-group">
    <label for="kids_lesson" class="w-100">
      担当レッスン
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    <select name="kids_lesson[]" class="form-control select2" placeholder="担当科目" required="true" multiple="multiple">
      <option value="">(選択してください)</option>
      @foreach($candidate_teachers->enable_subject as $index=>$subject)
        <option value="{{$subject['subject_key']}}">{{$subject['subject_name']}}</option>
      @endforeach
    </select>
  </div>
</div>
@endif
