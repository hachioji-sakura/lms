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
    <div class="row">
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
      <div class="col-12 col-md-6">
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

      <div class="col-12 col-md-6">
        <label>{{__('labels.school_grade_type')}}</label>
        @if(isset($item) && $_edit == true)
        <span>{{$item->type_name}}</span>
        <input type="hidden" name="type" value="{{$item->semester_no}}">
        @else
        <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
        <div class="input-group">
          @foreach(config('attribute.school_grade_type') as $key => $name)
          <div class="form-check">
            <label class="form-check-label" for="type_{{$key}}">
              <input class="frm-check-input icheck flat-green" type="radio" name="type" id="type_{{$key}}" value="{{$key}}" required="true">
              {{$name}}
            </label>
          </div>
          @endforeach
        </div>
        @endif
      </div>
    </div>

    <div class="row mt-2">
      <div class="col-12">
        <div class="form-group">
          <label for="body" class="w-100">
            {{__('labels.file')}}
            <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          </label>
          @if(isset($_edit) && $_edit == true && !empty($item['s3_url']))
          <label for="upload_file" class="w-100 upload_file">
            <a id="upload_file_link" href="{{$item['s3_url']}}" target="_blank" class="">{{$item['s3_alias']}}</a>
            <a href="javascript:void(0);" onClick="upload_file_clear();"class="btn btn-default btn-sm ml-1">
              <i class="fa fa-times"></i>
            </a>
          </label>
          <input type="hidden" name="upload_file_delete" value="0">
          <input type="hidden" name="upload_file_name" value="{{$item['s3_alias']}}">
          <script>
          $("input[name='upload_file']").hide();
          function upload_file_clear(){
            console.log("update_file_clear");
            $(".upload_file").hide();
            $("input[name='upload_file_delete']").val(1);
            $("input[name='upload_file']").show();
          }
          </script>
          @endif
          <input type="file" name="upload_file" class="form-control" placeholder="ファイル" required="true">
          @if ($errors->has('upload_file'))
          <span class="invalid-feedback">
          <strong>{{ $errors->first('upload_file') }}</strong>
          </span>
          @endif
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <label>{{__('labels.school_grade_reports')}}</label>
        <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
        <button class="btn btn-sm btn-primary add" type="button"><i class="fa fa-plus"></i>{{__('labels.add')}}</button>
      </div>
      @if($_edit == true && $item->school_grade_reports->count() > 0)
        @foreach($item->school_grade_reports as $report)
          @include("school_grades.add_report")
        @endforeach
      @else
        @include("school_grades.add_report")
      @endif
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
$("input[type=radio][name=type]").on("ifChecked",function(){
  var type = $(this).val();
  var data = @json(config('attribute.school_grade_type_points'));
  console.log(data[type]);

  $('select[name="report_point[]"] option').remove();

  $.each(data[type], function(index, value){
    $('select[name="report_point[]"]').append("<option value="+index+">"+value+"</option>");
  });

});
$("button.add").on("click",function(){
  $clone = $("div.report:first").clone(true);

  $clone.find("span").remove();
  $clone.find("select").select2({width:"100%",ariahidden:false});
  $clone.insertAfter($("div.report:last"));
  base.pageSettinged('school_grades_create');
});
$("button.delete").on("click",function(){
  if($('select[name="subject[]"]').length > 1 && $(this).parent().parent().attr("class") != "report"){
    $(this).parent().parent().parent().remove();
  }
});
</script>
