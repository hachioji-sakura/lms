<div class="col-12 subject_form">
  <div class="form-group">
    <label for="subject_level" class="w-100">
      @isset($title)
      {{$title}}
      @else
      ご希望の科目
      @endisset
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
      <th class="p-1 text-sm text-center">
        希望しない
      </th>
      <th class="p-1 text-sm text-center">
        補習授業
      </th>
      <th class="p-1 text-sm text-center">
        受験対策
      </th>
    </tr>
    @foreach(config('charge_subjects') as $grade => $subject_group)
      {{-- 学年ごと --}}
      @foreach($subject_group as $subject => $subject_data)
        {{-- 科目分類ごと --}}
        <?php $l1 = $loop->index; ?>
        @isset($subject_data['items'])
          @foreach($subject_data['items'] as $subject => $subject_name)
            {{-- 科目ごと --}}
            <tr class="grade-subject" alt="{{$grade}}">
            @if($l1===0 && $grade_display===true)
            <th class="p-1 text-center bg-gray" rowspan=100>{{$grade}}</th>
            @endif
            @if($loop->index===0 && $category_display===true)
            <th class="p-1 text-center bg-gray bd-light bd-r" rowspan={{count($subject_data['items'])}}>{{$subject_data['name']}}</th>
            @endif
            <th class="p-1 text-center bg-gray subject_name">{{$subject_name}}</th>
            @foreach($attributes['lesson_subject_level'] as $index => $name)
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
          @if($loop->index===0 && $grade_display===true)
          <th class="p-1 text-center bg-gray" rowspan={{count($subject_group)}}>{{$grade}}</th>
          @endif
          @if($category_display===true)
          <th class="p-1 text-center bg-gray bd-light bd-r">{{$subject_data['name']}}</th>
          @endif
          <th class="p-1 text-center bg-gray subject_name">{{$subject_data['name']}}</th>
          @foreach($attributes['lesson_subject_level'] as $index => $name)
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
      console.log($("input.subject_level[type='radio']", $(".carousel-item.active")).length);
      var _is_scceuss = false;
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
