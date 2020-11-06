<input type="hidden" name="subject_day_count_sum" value=0>
<div class="col-12">
  <label for="subject_level" class="w-100">
    <?php $__attribute_name = 'lesson_subject_level'; ?>
    科目ごとの授業数を入力してください
    <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
  </label>
</div>
<div class="col-7 col-md-7 subject_form">
  <div class="form-group">
    <table class="table" id="subject_table">
    <tr class="bg-gray">
      @if($grade_display===true)
      <th class="p-1">学年</th>
      @endif
      @if($category_display===true)
      <th class="p-1 text-sm text-center">分類</th>
      @endif
      <th class="p-1 text-sm text-center">科目</th>
      <th class="p-1 text-sm text-center">日数</th>
    </tr>
    @foreach(config('charge_subjects') as $grade => $subject_group)
      {{-- 学年ごと --}}
      <?php $l1 = 0; ?>
      @foreach($subject_group as $subject => $subject_data)
        {{-- 科目分類ごと --}}
        @isset($subject_data['items'])
          {{-- 3層科目（高校） --}}
          @foreach($subject_data['items'] as $subject => $subject_name)
            {{-- 科目ごと --}}
            <tr class="grade-subject" alt="{{$grade}}">
            @if($l1===0 && $grade_display===true)
            <?php $l1++; ?>
            <th class="p-1 text-center bg-gray" rowspan=100>{{$grade}}</th>
            @endif
            @if($loop->index===0 && $category_display===true)
            <th class="p-1 text-center bg-gray bd-light bd-r" rowspan={{count($subject_data['items'])}}>{{$subject_data['name']}}</th>
            @endif
            <th class="p-1 text-center bg-gray subject_name">{{$subject_name}}</th>
            <td class="p-1 text-center">
              <select name="{{$subject}}_day_count" class="form-control subject_day_count"  required="true" onChange='subject_day_count_onload()' validate="day_count_check()">
                @for($i=0;$i<10;$i++)
                <option value="{{$i}}"
                  >{{$i}}</option>
                @endfor
              </select>
            </td>
          </tr>
          @endforeach
        @else
          <tr class="grade-subject" alt="{{$grade}}">
          @if($l1===0 && $grade_display===true)
          <?php $l1++; ?>
          <th class="p-1 text-center bg-gray" rowspan={{count($subject_group)}}>{{$grade}}</th>
          @endif
          @if($category_display===true)
          <th class="p-1 text-center bg-gray bd-light bd-r">{{$subject_data['name']}}</th>
          @endif
          <th class="p-1 text-center bg-gray subject_name">{{$subject_data['name']}}</th>
          <td class="p-1 text-center">
            <select name="{{$subject}}_day_count" class="form-control subject_day_count"  required="true" onChange='subject_day_count_onload()' validate="day_count_check()">
              @for($i=0;$i<10;$i++)
              <option value="{{$i}}"
                >{{$i}}</option>
              @endfor
            </select>
          </td>
          </tr>
        @endisset
      @endforeach
    @endforeach
    </table>
  </div>
</div>
<div class="col-5 col-md-5">
  <table class="table">
  <tr>
    <th class="bg-gray">授業日数</th>
    <td class="w-40 text-right bg-warning">
      <span id="day_count" >0</span>
    </td>
  </tr>
  <tr>
    <th class="bg-gray">科目授業数</th>
    <td class="w-40 text-right bg-warning">
      <span id="subject_day_count_sum" >0</span>
    </td>
  </tr>
  </table>
</div>
@if($_teacher == false)
<script>
$(function(){
  subject_day_count_onload();
  $('select.subject_day_count').on('change', function(e){
    subject_day_count_onload();
  });
});
function day_count_check(){
  subject_day_count_onload();
  s1 = $('#day_count').html()|0;
  s2 = $('#subject_day_count_sum').html()|0;
  if(s1!=s2){
    front.showValidateError('#subject_table', '希望授業数と、科目授業数が一致しません');
    return false;
  }
  return true;
}
function subject_day_count_onload(){
  var s = 0;
  $('select.subject_day_count').each(function(){
    v = $(this).val();
    if(v) s+=v|0;
  });
  $('#subject_day_count_sum').html(s);
  $('input[name="subject_day_count_sum"]').val(s);
}
</script>
@endif
