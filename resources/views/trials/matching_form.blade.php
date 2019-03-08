@section('matching_form')
<div id="matching_form">
  <div class="row">
      <div class="col-12">
      <a data-toggle="collapse" data-parent="#matching_form" href="#page_item" class="" aria-expanded="true">
          体験お申込み内容（クリックで開閉）
      </a>
    </div>
  </div>
  <div class="collapse" id="page_item">
    @component('components.page_item', ['item' => $item, 'fields' => $fields, 'domain' => $domain])
      @slot('add_form')
      @endslot
    @endcomponent
  </div>
</div>
@endsection

@section('teacher_select_form')
  @if(count($candidate_teachers) > 0)
  <ul class="mailbox-attachments clearfix row">
    <li class="col-12 bg-light" accesskey="" target="">
      <div class="row">
        <div class="col-4 col-lg-4 col-md-4">
          講師
        </div>
        <div class="col-4 col-lg-4 col-md-4">
          希望日時1
        </div>
        <div class="col-4 col-lg-4 col-md-4">
          希望日時２
        </div>
    </li>
    @foreach($candidate_teachers as $teacher)
    <li class="col-12" accesskey="" target="">
      <div class="row">
        <div class="col-4 col-lg-4 col-md-4">
          <div class="w-100">
            <a href="/teachers/{{$teacher->id}}/calendar" target="_blank">
              <i class="fa fa-calendar-alt mr-2"></i>
              {{$teacher->name()}}
            </a>
          </div>
          <div class="w-100">
            担当可能科目
          </div>
          <div class="w-100 my-1">
            @foreach($teacher->enable_subject as $subject)
            <small class="badge badge-{{$subject['style']}} mt-1 mr-1">
              {{$subject["key"]}}
            </small>
            @endforeach
          </div>
          <div class="w-100">
            担当不可科目
          </div>
          <div class="w-100">
            @foreach($teacher->disable_subject as $subject)
            <small class="badge badge-{{$subject['style']}} mt-1 mr-1">
              {{$subject["key"]}}
            </small>
            @endforeach
          </div>
        </div>
        <div class="col-4 col-lg-4 col-md-4">
          @foreach($teacher->trial1 as $i=>$_list)
            @if($_list['free'])
            <div class="form-check ml-2">
              <input class="form-check-input icheck flat-green" type="radio" name="teacher_schedule" id="trial1_{{$teacher->id}}_{{$i}}"
               required="true"
               value="{{$teacher->id}}_{{$_list['start_time']}}_{{$_list['end_time']}}"
               teacher_id="{{$teacher->id}}"
               teacher_name="{{$teacher->name()}}"
               dulation="{{$_list['dulation']}}"
               start_time="{{$_list['start_time']}}"
               end_time="{{$_list['end_time']}}">
              <label class="form-check-label" for="trial1_{{$teacher->id}}_{{$i}}">
                {{$_list['dulation']}}
              </label>
            </div>
            @else
            <div class="form-check ml-2">
              <label class="form-check-label" for="trial1_{{$teacher->id}}_{{$i}}">
                <i class="fa fa-calendar-times mr-1"></i>
                {{$_list['dulation']}}
              </label>
            </div>
            @endif
          @endforeach
        </div>
        <div class="col-4 col-lg-4 col-md-4">
          @foreach($teacher->trial2 as $i => $_list)
            @if($_list['free'])
            <div class="form-check ml-2">
              <input class="form-check-input icheck flat-green" type="radio" name="teacher_schedule" id="trial2_{{$teacher->id}}_{{$i}}"
               required="true"
               value="{{$teacher->id}}_{{$_list['start_time']}}_{{$_list['end_time']}}"
               teacher_id="{{$teacher->id}}"
               teacher_name="{{$teacher->name()}}"
               dulation="{{$_list['dulation']}}"
               start_time="{{$_list['start_time']}}"
               end_time="{{$_list['end_time']}}">
              <label class="form-check-label" for="trial2_{{$teacher->id}}_{{$i}}">
                {{$_list['dulation']}}
              </label>
            </div>
            @else
            <div class="form-check ml-2">
              <label class="form-check-label" for="trial2_{{$teacher->id}}_{{$i}}">
                <i class="fa fa-calendar-times mr-1"></i>
                {{$_list['dulation']}}
              </label>
            </div>
            @endif
          @endforeach

        </div>
      </div>
    </li>
    @endforeach
  </ul>
  @else
  <div class="alert">
    <h4><i class="icon fa fa-exclamation-triangle"></i>データがありません</h4>
  </div>
  @endif
@endsection
@section('other_form')
<div class="row">
  <div class="col-12 mt-2">
  <div class="form-group">
    <label for="place" class="w-100">
      場所
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    <select name="place" class="form-control" placeholder="場所" required="true">
      <option value="">(選択してください)</option>
      @foreach($attributes['place'] as $index => $name)
        <option value="{{$index}}">{{$name}}</option>
      @endforeach
    </select>
  </div>
</div>
<div class="col-12">
  <div class="form-group">
    <label for="howto" class="w-100">
      その他、講師に連絡する内容について
      <span class="right badge badge-secondary ml-1">任意</span>
    </label>
    <textarea type="text" id="body" name="remark" class="form-control" placeholder="例：〇〇学校の受験希望をしている生徒様です。" ></textarea>
  </div>
</div>
<div class="col-12">
  <div class="form-group">
    <label for="matching_decide" class="w-100">
      講師を決めた理由は？
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    @foreach($attributes['matching_decide'] as $index => $name)
    <label class="mx-2">
      <input type="checkbox" value="{{ $index }}" name="matching_decide[]" class="icheck flat-green"  onChange="matching_decide_checkbox_change(this)" required="true">{{$name}}
    </label>
    @endforeach
  </div>
</div>
<div class="col-12 collapse matching_decide_word_form">
  <div class="form-group">
    <label for="matching_decide_word" class="w-100">
      その他の場合、理由を記述してください
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    <input type="text" id="matching_decide_word" name="matching_decide_word" class="form-control" placeholder="例：数学の受験対策を希望していたため" >
  </div>
</div>
<script>
function matching_decide_checkbox_change(obj){
  var is_other = $('input[type="checkbox"][name="matching_decide[]"][value="other"]').prop("checked");
  if(is_other){
    $(".matching_decide_word_form").collapse("show");
    $(".matching_decide_word_confirm").collapse("show");
  }
  else {
    $(".matching_decide_word_form").collapse("hide");
    $(".matching_decide_word_confirm").collapse("hide");
  }
}
</script>
</div>
@endsection
@section('confirm_form')
<div class="row">
  <div class="col-12 bg-warning p-2 pl-4 mb-4">
    <i class="fa fa-exclamation-triangle mr-1"></i>
    以下の体験授業内容を、担当講師に連絡します。
  </div>
  <div class="col-12 bg-info p-2 pl-4">
    <i class="fa fa-check mr-1"></i>
    体験授業予定
  </div>
  <div class="col-6 p-3" >
    <label class="w-100 font-weight-bold">
      日時
    </label>
    <div class="w-100"><span id="dulation"></span></div>
    <input type="hidden" name="start_time" value="">
    <input type="hidden" name="end_time" value="">
  </div>
  <div class="col-6 p-3" >
    <label class="w-100 font-weight-bold">
      校舎
    </label>
    <div class="w-100"><span id="place_name"></span></div>
  </div>
  <div class="col-6 p-3" >
    <label class="w-100 font-weight-bold">
      担当講師
    </label>
    <div class="w-100"><span id="teacher_name"></span></div>
    <input type="hidden" name="teacher_id" value="">
  </div>
  <div class="col-12 p-3 hr-1" >
    <label class="w-100 font-weight-bold">
      その他、講師に連絡する内容について
    </label>
    <div class="w-100"><span id="remark"></span></div>
  </div>
  <div class="col-12 p-3" >
    <label class="w-100 font-weight-bold">
      講師を決めた理由は？
    </label>
    <div class="w-100"><span id="matching_decide_name"></span></div>
  </div>
  <div class="col-12 p-3 matching_decide_word_confirm" >
    <label class="w-100 font-weight-bold">
      講師を決めた理由(その他）
    </label>
    <div class="w-100"><span id="matching_decide_word"></span></div>
  </div>
</div>
@endsection
