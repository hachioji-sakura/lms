<div id="{{$domain}}_create">
  @if(isset($_edit) && $_edit==true)
  <form id="edit" method="POST" action="/{{$domain}}/{{$item['id']}}" enctype="multipart/form-data">
    @method('PUT')
  @else
  <form id="edit" method="POST" action="/{{$domain}}" enctype="multipart/form-data">
  @endif
    @csrf
    <input type="text" name="dummy" style="display:none;" / >

    @if(isset($student_id))
      <input type="hidden" name="student_id" value="{{$student_id}}">
    @endif
    <div class="row mb-2">
      <div class="col-12">
        <label>{{__('labels.school_grade_reports')}}</label>
        <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
        <input type="text" name="name" class="form-control" placeholder="例:○○中学校　△学年　中間考査" required="true">
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <label>{{__('labels.school_grade_reports')}}</label>
        <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
        <button class="btn btn-sm btn-primary add" type="button"><i class="fa fa-plus"></i>{{__('labels.add')}}</button>
      </div>
      @if(isset($item) && $_edit == true && $item->school_grade_reports->count() > 0)
        @foreach($item->school_grade_reports as $report)
          @include("exams.add_result")
        @endforeach
      @else
        @include("exams.add_result")
      @endif
    </div>
    <div class="alert alert-warning text-sm pr-2">
      <b>
        <i class="icon fa fa-exclamation-triangle"></i>5段階評価の場合は2倍した値を選択してください。<br/>
        例:　評価値　4　→　入力値　8
      </b>
    </div>

    <div class="row mt-3">
      <div class="col-12 col-md-6 mb-1">
          <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="{{$domain}}_create">
            @if(isset($_edit) && $_edit==true)
              {{__('labels.update_button')}}
            @else
              登録する
            @endif
          </button>
          @if(isset($error_message))
            <span class="invalid-feedback d-block ml-2 " role="alert">
                <strong>{{$error_message}}</strong>
            </span>
          @endif
      </div>
      <div class="col-12 col-md-6 mb-1">
          <button type="reset" class="btn btn-secondary btn-block">
              キャンセル
          </button>
      </div>
    </div>
  </form>
</div>
<script>
$("button.add").on("click",function(){
  $clone = $("div.report_point:first").clone(true);

  $clone.find("span").remove();
  $clone.find("select").select2({width:"100%",ariahidden:false});
  $clone.insertAfter($("div.report_point:last"));
  base.pageSettinged('school_grades_create');
});
$("button.delete").on("click",function(){
  if($('select[name="subject[]"]').length > 1 && $(this).parent().parent().attr("class") != "report_point"){
    $(this).parent().parent().parent().remove();
  }
});
</script>
