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
    <table class="table table-striped">
    <tr class="bg-gray">
      <!-- th class="p-1">学年</th -->
      <th class="p-1 text-sm text-center">分類</th>
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
      @foreach($subject_group as $subject => $subject_data)
        <?php $l1 = $loop->index; ?>
        @isset($subject_data['items'])
          @foreach($subject_data['items'] as $subject => $subject_name)
            <tr class="grade-subject" alt="{{$grade}}">
            @if($l1===0)
            <!-- th class="p-1 text-center bg-gray" rowspan=100>{{$grade}}</th -->
            @endif
            @if($loop->index===0)
            <th class="p-1 text-center bg-gray bd-light bd-r" rowspan={{count($subject_data['items'])}}>{{$subject_data['name']}}</th>
            @endif
            <th class="p-1 text-center bg-gray subject_name">{{$subject_name}}</th>
            @foreach($attributes['lesson_subject_level'] as $index => $name)
              <td class="p-1 text-center">
              <input type="radio" value="{{ $index }}" name="{{$subject}}_level" class="icheck
                @if($loop->index===0)
                flat-grey
                @else
                flat-green
                @endif
               subject_level"  required="true"
              @if(isset($item) && isset($item->user) && $item->user->has_tag($subject.'_level', $index)===true || (!isset($_edit) && $loop->index == 0))
               checked
              @endif
              >
            </td>
            @endforeach
          </tr>
          @endforeach
        @else
          <tr class="grade-subject" alt="{{$grade}}">
          @if($loop->index===0)
          <!-- th class="p-1 text-center bg-gray" rowspan={{count($subject_group)}}>{{$grade}}</th -->
          @endif
          <th class="p-1 text-center bg-gray bd-light bd-r">{{$subject_data['name']}}</th>
          <th class="p-1 text-center bg-gray subject_name">{{$subject_data['name']}}</th>
          @foreach($attributes['lesson_subject_level'] as $index => $name)
            <td class="text-center">
            <input type="radio" value="{{ $index }}" name="{{$subject}}_level" class="icheck
            @if($loop->index===0)
            flat-grey
            @else
            flat-green
            @endif
             subject_level"  required="true"
            @if((isset($_edit) && isset($item) && isset($item->user) && $item->user->has_tag($subject.'_level', $index)===true) || (!isset($_edit) && $loop->index == 0))
             checked
            @endif
            >
          </td>
          @endforeach
          </tr>
        @endisset
      @endforeach
    @endforeach
    </table>
  </div>
</div>
