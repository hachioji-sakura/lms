<div class="col-12 subject_form">
  <div class="form-group">
    <label for="subject_level" class="w-100">
      <?php $__attribute_name = 'lesson_subject_level'; ?>
      @if(isset($_teacher) && $_teacher===true)
      <?php $__attribute_name = 'charge_subject_level'; ?>
      {{__('labels.charge_subject')}}
      @else
      ご希望の科目
      @endif
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
      @foreach($attributes[$__attribute_name] as $index => $name)
      <th class="p-1 text-sm text-center">
        {{$name}}
      </th>
      @endforeach
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
            @foreach($attributes[$__attribute_name] as $index => $name)
              {{-- 科目レベルごと --}}
              <td class="p-1 text-center">
              <input type="radio" value="{{ $index }}" name="{{$subject}}_level" class="icheck
                @if($loop->index===0)
                flat-grey
                @else
                flat-green
                @endif
               subject_level"  required="true"
              @if($_edit===true)
                @if($item->has_tag($subject.'_level', $index)===true)
                  checked
                @endif
                @if($item->has_tag($subject.'_level')===false && $loop->index==0)
                  checked
                @endif
              @else
                @if($loop->index == 0)
                  checked
                @endif
              @endif
              validate="subject_validate()">
            </td>
            @endforeach
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
          @foreach($attributes[$__attribute_name] as $index => $name)
            {{-- 科目レベルごと --}}
            <td class="text-center">
            <input type="radio" value="{{ $index }}" name="{{$subject}}_level" class="icheck
            @if($loop->index===0)
            flat-grey
            @else
            flat-green
            @endif
             subject_level"  required="true"
             @if($_edit===true)
               @if($item->has_tag($subject.'_level', $index)===true)
                checked
               @endif
               @if($item->has_tag($subject.'_level')===false && $loop->index==0)
                checked
               @endif
             @else
               @if($loop->index == 0)
                checked
               @endif
             @endif
             validate="subject_validate()">
          </td>
          @endforeach
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
