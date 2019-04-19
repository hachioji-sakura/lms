<div class="col-12 subject_form">
  <div class="form-group">
    <label for="subject_level" class="w-100">
      <?php $__attribute_name = 'lesson_subject_level'; ?>
      @if(isset($_teacher) && $_teacher===true)
      <?php $__attribute_name = 'charge_subject_level'; ?>
      担当可能科目
      @else
      ご希望の科目
      @endif
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    <table class="table table-striped" id="subject_table">
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
                @if($item->user->has_tag($subject.'_level', $index)===true)
                  checked
                @endif
                @if($item->user->has_tag($subject.'_level')===false && $loop->index)
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
               @if($item->user->has_tag($subject.'_level', $index)===true)
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
    <script>
    function subject_validate(){
      var _is_scceuss = false;
      var is_school = $('input[type="checkbox"][name="lesson[]"][value="1"]').prop("checked");
      if(!is_school) return true;
      if( $("input.subject_level[type='radio']", $(".carousel-item.active")).length > 0){
        $("input.subject_level[type='radio'][value!=1]:checked", $(".carousel-item.active")).each(function(index, value){
          var val = $(this).val();
          console.log(val);
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
    </script>
  </div>
</div>
