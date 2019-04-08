
@section('teacher_select_form')
  @if(count($candidate_teachers) > 0)
  <ul class="mailbox-attachments clearfix row">
    <li class="col-12 bg-light" accesskey="" target="">
      <div class="row">
        <div class="col-12">
          担当講師
        </div>
      </div>
    </li>
    @foreach($candidate_teachers as $teacher)
    <li class="col-6" accesskey="" target="">
      <div class="row">
        <div class="col-12 mb-2">
          <div class="row">
            <div class="col-4">
              <div class="description-block">
                <h5 class="description-header text-center">
                  <img src="{{$teacher->user->icon()}}" class="img-circle mw-64px" alt="User Image">
                  <br>
                    {{$teacher->name()}}
                </h5>
                <span class="description-text">
                  <a href="/teachers/{{$teacher->id}}/calendar" target="_blank" class="text-sm">
                    <i class="fa fa-calendar-alt mr-1"></i>
                    授業予定
                  </a>
                  @foreach($teacher->user->tags as $tag)
                    @if($user->role==="manager" && $tag->tag_key=="teacher_character")
                      <small class="badge badge-info mt-1 mr-1">
                        {{$tag->name()}}
                      </small>
                    @endif
                  @endforeach
                </span>
              </div>
            </div>
            <div class="col-4">
              <div class="description-block">
                <h5 class="description-header">
                  <i class="fa fa-check-circle mr-1"></i>
                  担当可
                </h5>
                <span class="description-text">
                  @if(count($teacher->enable_subject)<1)
                    <small class="badge badge-success mt-1 mr-1">
                      なし
                    </small>
                  @else
                    @foreach($teacher->enable_subject as $subject)
                    <small class="badge badge-{{$subject['style']}} mt-1 mr-1">
                      {{$subject["key"]}}
                    </small>
                    @endforeach
                  @endif
                </span>
              </div>
            </div>
            <div class="col-4">
              <div class="description-block">
                <h5 class="description-header">
                  <i class="fa fa-times-circle mr-1"></i>
                  担当不可
                </h5>
                <span class="description-text">
                  @if(count($teacher->disable_subject)<1)
                    <small class="badge badge-success mt-1 mr-1">
                      なし
                    </small>
                  @else
                    @foreach($teacher->disable_subject as $subject)
                    <small class="badge badge-{{$subject['style']}} mt-1 mr-1">
                      {{$subject["key"]}}
                    </small>
                    @endforeach
                  @endif
                </span>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12 mb-2">
          <div class="description-block">
            <h5 class="description-header">
                <i class="fa fa-calendar-check mr-1"></i>
                定期スケジュール　ー空き情報ー
            </h5>
            <span class="description-text">
              <table class="table table-striped border-bottom">
              <tr class="bg-gray">
                @foreach($attributes['lesson_week'] as $index => $name)
                <th class="p-1 text-center border-right lesson_week_label
                @if($index==="mon") border-left
                @elseif($index==="sat") text-primary
                @elseif($index==="sun") text-danger
                @endif
                " alt="{{$index}}">
                   {{$name}}
                </th>
                @endforeach
              </tr>
              <tr class="">
                @foreach($attributes['lesson_week'] as $index => $name)
                <td class="p-1 text-center @if($loop->index===0) border-left @endif border-right" id="">
                  @isset($teacher->match_schedule['count'][$index])
                    @if($teacher->match_schedule['count'][$index] > 0)
                    <small class="badge badge-primary mx-2">
                      空き{{$teacher->match_schedule['count'][$index]}}コマ
                    </small>
                    @else
                    -
                    @endif
                  @else
                  -
                  @endisset
                </td>
                @endforeach
              </tr>
              </table>
            </span>
          </div>
        </div>
        <div class="col-12 mb-2">
          <a href="/{{$domain}}/{{$item->id}}/to_calendar?teacher_id={{$teacher->id}}" role="button" class="btn btn-block btn-info">担当講師にする　<i class="fa fa-chevron-right ml-2"></i></a>
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
        担当科目
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      <select name="place" class="form-control" placeholder="場所" required="true">
        <option value="">(選択してください)</option>
        @foreach($candidate_teachers[0]->enable_subject as $subject)
          {{$subject["key"]}}
          <option value="{{$index}}">{{$name}}</option>
        @endforeach
      </select>
    </div>
  </div>
  <div class="col-12 mt-2">
    <div class="form-group">
      {{-- TODO:lesson_place＝申し込み時に入力された、場所概要から、placeを絞り込む --}}
      <label for="place" class="w-100">
        教室
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
    <div class="w-100"><span id='teacher_name'></span></div>
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
