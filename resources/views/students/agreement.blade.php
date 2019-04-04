@include('parents.create_form')

@component('components.page', ['item' => $item, 'fields' => $fields, 'domain' => $domain])
{{-- メッセージカスタマイズ --}}
  @slot('page_message')
    @if(isset($page_message) && !empty(trim($page_message)))
      {{$page_message}}
    @endif
  @endslot
  {{-- 表示部分カスタマイズ --}}
  @slot('field_logic')
  <div class="row">
    <div class="col-12 bg-info p-2 pl-4">
      <i class="fa fa-user-graduate mr-1"></i>
      生徒様情報
    </div>
    <div class="col-6 p-3 font-weight-bold" >氏名・フリガナ</div>
    <div class="col-6 p-3">
      <ruby style="ruby-overhang: none">
        <rb>{{$student->name()}}</rb>
        <rt>{{$student->kana()}}</rt>
      </ruby>
    </div>
    <div class="col-6 p-3 font-weight-bold" >性別</div>
    <div class="col-6 p-3">{{$student->gender()}}</div>
    <div class="col-6 p-3 font-weight-bold" >生年月日</div>
    <div class="col-6 p-3">{{$student->birth_day()}}</div>
    <div class="col-6 p-3 font-weight-bold" >学年</div>
    <div class="col-6 p-3">{{$student->grade()}}</div>
    <div class="col-6 p-3 font-weight-bold school_name_confirm" >学校名</div>
    <div class="col-6 p-3">{{$student->school_name()}}</div>
  </div>
  <div class="row">
    <div class="col-12 bg-info p-2 pl-4">
      <i class="fa fa-chalkboard-teacher mr-1"></i>
      お申込み情報
    </div>
    <div class="col-6 p-3 font-weight-bold" >ご希望の校舎</div>
    <div class="col-6 p-3">{{$student->lesson_place()}}</div>
    <div class="col-6 p-3 font-weight-bold" >ご希望のレッスン</div>
    <div class="col-6 p-3"><span id="lesson_name"></span></div>
    <div class="col-12 p-3 font-weight-bold">
      ご希望の曜日・時間帯
    </div>
    <div class="col-12">
      <div class="form-group">
        <table class="table table-striped">
        <tr class="bg-gray">
          <th class="p-1 text-center">時間帯 / 曜日</th>
          @foreach($attributes['lesson_week'] as $index => $name)
          <th class="p-1 text-center lesson_week_label" atl="{{$index}}">
             {{$name}}
          </th>
          @endforeach
        </tr>
        @foreach($attributes['lesson_time'] as $index => $name)
        <tr class="">
          <th class="p-1 text-center bg-gray text-sm lesson_week_time_label">{{$name}}</th>
          @foreach($attributes['lesson_week'] as $week_code => $week_name)
          <td class="p-1 text-center" id="lesson_{{$week_code}}_time_{{$index}}_name">
            -
          </td>
          @endforeach
        </tr>
        @endforeach
        </table>
      </div>
    </div>
    <div class="col-12 p-3 font-weight-bold">
      ご希望の科目
    </div>
    <div class="col-12">
      <div class="form-group">
        <table class="table table-striped">
        <tr class="bg-gray">
          <th class="p-1 text-sm text-center">分類</th>
          <th class="p-1 text-sm text-center">科目</th>
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
                @if($loop->index===0)
                <th class="p-1 text-center bg-gray" rowspan={{count($subject_data['items'])}}>{{$subject_data['name']}}</th>
                @endif
                <th class="p-1 text-center bg-gray subject_name">{{$subject_name}}</th>
                @foreach($attributes['charge_subject_level'] as $index => $name)
                  @if($loop->index == 0)
                    @continue
                  @elseif($loop->index >= 3)
                    @break
                  @endif
                  <td class="text-center" id="{{$subject}}_level_{{$index}}_name">
                    -
                  </td>
                </td>
                @endforeach
              </tr>
              @endforeach
            @else
              <tr class="grade-subject" alt="{{$grade}}">
              <th class="p-1 text-center bg-gray">{{$subject_data['name']}}</th>
              <th class="p-1 text-center bg-gray subject_name">{{$subject_data['name']}}</th>
              @foreach($attributes['charge_subject_level'] as $index => $name)
                @if($loop->index == 0)
                  @continue
                @elseif($loop->index >= 3)
                  @break
                @endif
                <td class="text-center" id="{{$subject}}_level_{{$index}}_name">
                  -
                </td>
              @endforeach
              </tr>
            @endisset
          @endforeach
        @endforeach
        </table>
      </div>
    </div>
    <div class="col-6 p-3 font-weight-bold english_confirm" >ご希望の英会話講師</div>
    <div class="col-6 p-3 english_confirm"><span id="english_teacher_name"></span></div>
    <div class="col-6 p-3 font-weight-bold piano_confirm" >ピアノのご経験について</div>
    <div class="col-6 p-3 piano_confirm"><span id="piano_level_name"></span></div>
    <div class="col-6 p-3 font-weight-bold" >ご要望について</div>
    <div class="col-6 p-3"><span id="remark"></span></div>
  </div>
  @endslot
  {{-- フォーム部分カスタマイズ --}}
  @slot('forms')
    @if(isset($forms) && !empty(trim($forms)))
      {{$forms}}
    @endif
  @endslot

@endcomponent
