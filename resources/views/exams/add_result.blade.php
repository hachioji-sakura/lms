<div class="col-12 col-md-12 mb-1 report_point">
  <div class="row">
    <div class="col-1">
      <button type="button" class="btn btn-tool delete float-right"><i class="fa fa-times-circle"></i></button>
    </div>
    <div class="col-2">
      <select name="subject[]" width="100%" class="form-control select2">
        <option value=" ">{{__('labels.selectable')}}</option>
        @foreach($subjects as $key => $name)
        <option value="{{$key}}" {{$_edit == true &&  isset($report) && $report->subject_id == $key ? "selected" : ''}}>{{$name}}</option>
        @endforeach
      </select>
    </div>
    <div class="col-2">
      <div class="row">
        <div class="col-5">
          <input type="text" name="point[]" class="form-control" placeholder="得点" required="true" inputtype="numeric">
        </div>
        <div class="col-1">
          /
        </div>
        <div class="col-5">
          <input type="text" name="max_point[]" class="form-control" placeholder="満点" required="true" inputtype="numeric">
        </div>
      </div>
    </div>
    <div class="col-2">
      <input type="text" name="average_point[]" class="form-control" placeholder="平均点" required="true" inputtype="numeric">
    </div>
    <div class="col-2">
      <input type="text" name="deviation[]" class="form-control" placeholder="偏差値" required="true" inputtype="numeric">
    </div>
    <div class="col-12">
      <div class="form-group">
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
        $("input[name='upload_file[]']").hide();
        function upload_file_clear(){
          console.log("update_file_clear");
          $(".upload_file").hide();
          $("input[name='upload_file_delete']").val(1);
          $("input[name='upload_file']").show();
        }
        </script>
        @endif
        <input type="file" name="upload_file" class="form-control" placeholder="ファイル" >
        @if ($errors->has('upload_file'))
        <span class="invalid-feedback">
        <strong>{{ $errors->first('upload_file') }}</strong>
        </span>
        @endif
      </div>
    </div>
  </div>
</div>
