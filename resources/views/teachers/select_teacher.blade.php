{{--
カレンダー系統の機能は先に講師を選択しないと、
フォームの複雑性が増すので、講師選択後にメインの画面に遷移するようにする
--}}
<div id="select_teacher">
@if(isset($page_message))
  <h6>{{$page_message}}</h6>
@endif
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <label for="title" class="w-100">
          {{__('labels.teachers')}}
          @if(isset($lesson_id) && $lesson_id>0)
          <input type="hidden" name="lesson_id" value="{{$lesson_id}}" >
            （レッスン：{{$attributes["lesson"][$lesson_id]}}）
          @endif
          @if(isset($student_id) && $student_id>0)
          <input type="hidden" name="student_id" value="{{$student_id}}" >
          @endif
          @if(isset($trial_id) && $trial_id>0)
          <input type="hidden" name="trial_id" value="{{$trial_id}}" >
          @endif
          @if(isset(request()->work) && request()->work>0)
          <input type="hidden" name="work" value="{{request()->work}}" >
          @endif
        </label>
        <select name="teacher_id" class="form-control select2"  width=100% required="true" >
          <option value="">{{__('labels.selectable')}}</option>
          @foreach($teachers as $teacher)
             <option
             value="{{ $teacher->id }}"
             >{{$teacher->name()}}</option>
          @endforeach
        </select>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-12 col-md-6 mb-1">
      <a href="javascript:void(0);" role="button" class="btn btn-primary btn-block" onClick="teacher_selected()">
        <i class="fa fa-arrow-circle-right mr-1"></i>
        {{__('labels.teachers')}}{{__('labels.select')}}
      </a>
    </div>
    <div class="col-12 col-md-6 mb-1">
      <a href="javascript:void(0);" data-dismiss="modal" role="button" class="btn btn-secondary btn-block float-left mr-1">
        <i class="fa fa-times-circle mr-1"></i>
        {{__('labels.cancel_button')}}
      </a>
    </div>
  </div>
</div>
<script>
$(function(){
  base.pageSettinged('select_teacher',null);
});
//ダイアログでサブページを開く場合、
function teacher_selected(){
  console.log("teacher_selected");
  var teacher_id = $("select[name='teacher_id'] option:selected").val();
  if(front.validateFormValue('select_teacher')){
    var _title = $('#subDialog .page_title').text();
    var _url = '/{{$domain}}/create?teacher_id='+teacher_id;
    var lesson_id = $('input[name="lesson_id"]').val();
    var trial_id = $('input[name="trial_id"]').val();
    var work = $('input[name="work"]').val();
    console.log(_url);
    if(lesson_id|0>0) _url +='&lesson_id='+lesson_id;
    if(trial_id|0>0) _url +='&trial_id='+trial_id;
    if(work|0>0) _url +='&work='+work;
    base.showPage("dialog", "subDialog", _title, _url);
  }
}
</script>
