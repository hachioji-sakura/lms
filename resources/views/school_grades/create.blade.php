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
      </div>
      @include("school_grades.add_report")
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

  $('select[name="report_point"] option').remove();

  $.each(data[type], function(index, value){
    $('select[name="report_point"]').append("<option value="+index+">"+value+"</option>");
  });
  $('div.reports').children().remove();

});
$("button.add").on("click",function(){
  var subject = $('select[name="subject"] option:selected');
  var report = $('select[name="report_point"] option:selected');
  if(subject.val() != " "){
    var subject_id = subject.val();
    var subject_name = subject.text();
    var report_point = report.val();
    var report_point_name = report.text();
    console.log(report_point_name);
    var data = [
      '<div class="col-6">',
        '<label>'+subject_name+':'+report_point_name+'</label>',
        '<input type="hidden" name="reports['+subject_id+']" value="'+report_point+'">',
        '<button type="button" class="btn btn-default btn-sm float-right delete"><i class="fa fa-times"></i></button>',
      '</div>',
    ].join('');

    $('div.reports').append(data);
  }
});
$('body').on('click','.delete',function(){
  console.log("hoge");
  $(this).parent().remove();
});
</script>
