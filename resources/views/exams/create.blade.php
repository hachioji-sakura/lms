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
      <div class="col-12 mb-2">
        <label>{{__('labels.name')}}</label>
        <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
        <input type="text" name="name" class="form-control" placeholder="例:○○中学校　△学年　中間考査" required="true">
      </div>
      <div class="col-12 col-md-6 mb-2">
        <label>{{__('labels.grade')}}</label>
        @if(isset($item) && $_edit == true)
        <span>{{$item->grade_name}}</span>
        <input type="hidden" name="grade" value="{{$item->grade}}">
        @else
        <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
        <select name="grade" id="select_grade" width="100%" class="form-control select2">
          <option value=" ">{{__('labels.selectable')}}</option>
          @foreach($grades as $key => $name)
          <option value="{{$key}}"
            {{$student->grade() == $name ? "selected" : ""}}
          >
          {{$name}}</option>
          @endforeach
        </select>
        @endif

      </div>
      <div class="col-12 col-md-6 mb-2">
        <label>{{__('labels.semester')}}</label>
        @if(isset($item) && $_edit == true)
        <span>{{$item->semester_name}}</span>
        <input type="hidden" name="semester_no" value="{{$item->semester_no}}">
        @else
        <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
        <div class="input-group">
          @foreach(config('attribute.semester_no') as $key => $name)
          <div class="form-check">
            <label class="form-check-label" for="type_{{$key}}">
              <input class="frm-check-input icheck flat-green" type="radio" name="semester_no" id="type_{{$key}}" value="{{$key}}" required="true">
              {{$name}}
            </label>
          </div>
          @endforeach
        </div>
        @endif
      </div>
    </div>

    <div class="col-12 col-md-6 mb-2">
      <label>{{__('labels.type')}}</label>
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
      <select name="type" id="select_type" width="100%" class="form-control select2">
        @foreach(config('attribute.exam_type') as $key => $name)
        <option value="{{$key}}"
          {{$_edit == true && $item->type == $key ? "selected" : ""}}
        >
        {{$name}}</option>
        @endforeach
      </select>
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
