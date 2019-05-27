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
          講師
        </label>
        <select name="teacher_id" class="form-control select2"  width=100% placeholder="講師" required="true" >
          <option value="">(選択)</option>
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
    <div class="col-12 col-lg-6 col-md-6 mb-1">
      <a href="javascript:void(0);" role="button" class="btn btn-primary btn-block" onClick="teacher_selected()">
        <i class="fa fa-arrow-circle-right mr-1"></i>
        講師選択
      </a>
    </div>
    <div class="col-12 col-lg-6 col-md-6 mb-1">
      <a href="javascript:void(0);" data-dismiss="modal" role="button" class="btn btn-secondary btn-block float-left mr-1">
        <i class="fa fa-times-circle mr-1"></i>
        キャンセル
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
  var teacher_id = $("select[name='teacher_id'] option:selected").val();
  if(front.validateFormValue('select_teacher')){
    var _title = $('#subDialog .page_title').text();
    base.showPage("dialog", "subDialog", _title, '/{{$domain}}/create?origin=teachers&item_id='+teacher_id);
  }
}
</script>
