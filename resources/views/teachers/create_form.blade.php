@section('account_form')
<div class="row">
  <div class="col-12">
    <h5 class="bg-info p-1 pl-2 mb-4">
      <i class="fa fa-key mr-1"></i>
      ログイン情報
    </h5>
  </div>
  <div class="col-12">
    <div class="form-group">
      <label for="email">
        メールアドレス
      </label>
      <h5>{{$item->email}}</h5>
      <input type="hidden" name="email" value="{{$item->email}}" />
    </div>
  </div>
  <div class="col-12">
    <div class="form-group">
      <label for="password">
        パスワード
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      <input type="password" id="password" name="password" class="form-control" placeholder="半角英数8文字以上16文字未満" minlength=8 maxlength=16 required="true">
    </div>
  </div>
  <div class="col-12">
    <div class="form-group">
      <label for="password-confirm">
        パスワード（確認）
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      <input type="password" id="password-confirm" name="password-confirm" class="form-control" placeholder="半角英数8文字以上16文字未満" minlength=8 maxlength=16 required="true" equal="password" equal_error="パスワードが一致しません">
    </div>
  </div>
  <div class="col-12">
    <h6 class="text-sm p-1 pl-2 mt-2 text-danger" >
      ※システムにログインする際、メールアドレスとパスワードが必要となります。
    </h6>
  </div>

</div>
@endsection



@section('teacher_form')
<div class="row">
  <div class="col-12">
    <h5 class="bg-info p-1 pl-2 mb-4">
      <i class="fa fa-user-friends mr-1"></i>
      講師情報
    </h5>
  </div>
  <div class="col-6 col-lg-6 col-md-6">
    <div class="form-group">
      <label for="name_last">
        氏
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      <input type="text" id="name_last" name="name_last" class="form-control" placeholder="例：八王子" required="true" inputtype="zenkaku" value="{{$item->name_last}}">
    </div>
  </div>
  <div class="col-6 col-lg-6 col-md-6">
    <div class="form-group">
      <label for="name_first">
        名
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      <input type="text" id="name_first" name="name_first" class="form-control" placeholder="例：桜" required="true" inputtype="zenkaku" value="{{$item->name_first}}">
    </div>
  </div>
  <div class="col-6 col-lg-6 col-md-6">
    <div class="form-group">
      <label for="kana_last">
        氏（カナ）
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      <input type="text" id="kana_last" name="kana_last" class="form-control" placeholder="例：ハチオウジ" required="true" inputtype="zenkakukana"
      @if(isset($_edit))
      value="{{$item->kana_last}}"
      @endif
       >
    </div>
  </div>
  <div class="col-6 col-lg-6 col-md-6">
    <div class="form-group">
      <label for="kana_first">
        名（カナ）
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      <input type="text" id="kana_first" name="kana_first" class="form-control" placeholder="例：サクラ" required="true" inputtype="zenkakukana"
      @if(isset($_edit))
      value="{{$item->kana_first}}"
      @endif
       >
    </div>
  </div>
  <div class="col-12 mb-2">
    @component('components.select_birthday', ['item' => $item])
    @endcomponent
  </div>
  <div class="col-12 mb-2">
    @component('components.select_gender', ['item' => $item])
    @endcomponent
  </div>
  <div class="col-12">
    <div class="form-group">
      <label for="phone_no">
        連絡先
        <span class="right badge badge-danger ml-1">必須</span>
        <span class="text-sm">ハイフン(-)不要</span>
      </label>
      <input type="text" id="phone_no" name="phone_no" class="form-control" placeholder="例：09011112222"  inputtype="number" required="true" maxlength=14
      @if(isset($_edit))
       value="{{$item->phone_no}}"
      @endif
       >
    </div>
  </div>
</div>
@endsection

@section('lesson_week_form')
<div class="row">
  <div class="col-12">
    <div class="form-group">
      <label for="lesson" class="w-100">
        担当教室
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      @foreach($attributes['lesson'] as $index => $name)
      <label class="mx-2">
        <input type="checkbox" value="{{ $index }}" name="lesson[]" class="flat-red" required="true"
        @if(isset($_edit) && $item->user->has_tag('lesson', $index)===true)
       checked
        @endif
        >{{$name}}
      </label>
      @endforeach
    </div>
  </div>
  <div class="col-12">
    <div class="form-group">
      <label for="subject_level" class="w-100">
        授業スケジュール
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      <table class="table table-condensed">
      <tr class="bg-gray">
        <th class="p-1 text-center">時間帯 / 曜日</th>
        @foreach($attributes['lesson_week'] as $index => $name)
        <th class="p-1 text-center">
           {{$name}}
        </th>
        @endforeach
      </tr>
      <tr class="">
        <th class="p-1 text-center bg-warning">担当不可</th>
        @foreach($attributes['lesson_week'] as $week_code => $week_name)
        <td class="p-1 text-center bg-warning">
          <input type="checkbox" value="disabled" name="lesson_{{$week_code}}_time[]" class="flat-red"  required="true"  onChange="lesson_week_disabled_change(this)"
            @if($item->user->has_tag('lesson_'.$week_code.'_time', 'disabled')===true)
           checked
            @endif
           >
        </td>
        @endforeach
      </tr>
      @foreach($attributes['lesson_time'] as $index => $name)
      <tr class="">
        <th class="p-1 text-center bg-gray">{{$name}}</th>
        @foreach($attributes['lesson_week'] as $week_code => $week_name)
        <td class="p-1 text-center">
          <input type="checkbox" value="{{ $index }}" name="lesson_{{$week_code}}_time[]" class="flat-red"  required="true"
          @if($item->user->has_tag('lesson_'.$week_code.'_time', $index)===true)
         checked
          @endif
          >
        </td>
        @endforeach
      </tr>
      @endforeach
      </table>
      <script>
      function lesson_week_disabled_change(obj){
        var _name = $(obj).attr("name");
        var _checked = $(obj).prop("checked");
        console.log(_name+":"*_checked)
        if(_checked){
          $('input[type="checkbox"][name="'+_name+'"]').each(function(i, e){
            if($(e).attr("value") !== "disabled") {
              $(this).prop('disabled', true);
              $(this).iCheck('uncheck');
              $(this).iCheck('disable');
            }
          });
        }
        else {
          $('input[type="checkbox"][name="'+_name+'"]').each(function(i, e){
            if($(e).attr("value") !== "disabled"){
              $(this).prop('disabled', false);
              $(this).iCheck('enable');
            }
          });
        }
      }
      </script>
    </div>
  </div>
</div>
@endsection

@section('subject_form')
<div class="row">
  <div class="col-12">
    <div class="form-group">
      <label for="subject_level" class="w-100">
        担当可能科目
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      <table class="table table-condensed">
      <tr class="bg-gray">
        <th class="p-1">学年</th>
        <th class="p-1">分類</th>
        <th class="p-1">科目</th>
        @foreach($attributes['charge_subject_level'] as $index => $name)
        <th class="p-1">
           {{$name}}
        </th>
        @endforeach
      </tr>
      @foreach(config('charge_subjects') as $grade => $subject_group)
        @foreach($subject_group as $subject => $subject_data)
          <?php $l1 = $loop->index; ?>
          @isset($subject_data['items'])
            @foreach($subject_data['items'] as $subject => $subject_name)
              <tr>
              @if($l1===0)
              <th class="p-1 text-center bg-gray" rowspan=100>{{$grade}}</th>
              @endif
              @if($loop->index===0)
              <th class="p-1 text-center bg-light" rowspan={{count($subject_data['items'])}}>{{$subject_data['name']}}</th>
              @endif
              <th class="p-1 text-center bg-light">{{$subject_name}}</th>
              @foreach($attributes['charge_subject_level'] as $index => $name)
              <td class="p-1 text-center">
                <input type="radio" value="{{ $index }}" name="{{$subject}}_level" class="flat-red"  required="true"
                @if($item->user->has_tag($subject.'_level', $index)===true || (!isset($_edit) && $loop->index == 0))
                 checked
                @endif
                >
              </td>
              @endforeach
            </tr>
            @endforeach
          @else
            <tr>
            @if($loop->index===0)
            <th class="p-1 text-center bg-gray" rowspan={{count($subject_group)}}>{{$grade}}</th>
            @endif
            <th class="p-1 text-center bg-light">{{$subject_data['name']}}</th>
            <th class="p-1 text-center bg-light">{{$subject_data['name']}}</th>
            @foreach($attributes['charge_subject_level'] as $index => $name)
            <td class="text-center">
              <input type="radio" value="{{ $index }}" name="{{$subject}}_level" class="flat-red"  required="true"
              @if((isset($_edit) && $item->user->has_tag($subject.'_level', $index)===true) || (!isset($_edit) && $loop->index == 0))
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
</div>
@endsection
