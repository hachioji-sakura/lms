<div class="col-12 subject_form">
  <div class="form-group">
    <label for="subject_level" class="w-100">
      <?php $__attribute_name = 'lesson_subject_level'; ?>
      科目ごとの授業数を入力してください
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
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
              <input type="text" name="{{$subject}}_day_count" class="form-control" value="0" inuttype="number" minlength="1" maxlength="2" minvalue="0">
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
            <input type="text" name="{{$subject}}_day_count" class="form-control" value="0" inuttype="number" minlength="1" maxlength="2" minvalue="0">
          </td>
          </tr>
        @endisset
      @endforeach
    @endforeach
    </table>
    @if($_teacher == false)
    <script>
    $(function(){
      subject_onload();
    });
    </script>
    @endif
  </div>
</div>
