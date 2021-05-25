<div id="{{$domain}}_create">
  @if(isset($_edit) && $_edit==true)
  <form id="edit" method="POST" action="/{{$domain}}/{{$item['id']}}" enctype="multipart/form-data">
    @method('PUT')
  @else
  <form id="edit" method="POST" action="/{{$domain}}" enctype="multipart/form-data">
  @endif
    @csrf
    <input type="text" name="dummy" style="display:none;" / >

    @if(isset($exam_id))
      <input type="hidden" name="exam_id" value="{{$exam_id}}">
    @endif
    <div class="col-12 col-md-12 mb-1 report_point">
      <div class="row">
        <div class="col-12 mb-2">
          <label>{{__('labels.subject')}}</label>
          <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          <select name="subject_id" width="100%" class="form-control select2" required="true">
            <option value=" ">{{__('labels.selectable')}}</option>
            @foreach($subjects as $key => $name)
            <option value="{{$key}}" {{$_edit == true && $item->subject_id == $key ? "selected" : ''}}>{{$name}}</option>
            @endforeach
          </select>
        </div>
        <div class="col-12 mb-2">
          <label>{{__('labels.point')}}</label>
          <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          <div class="row">
            <div class="col-5">
              <input type="text" name="point" class="form-control" placeholder="得点" required="true" inputtype="numeric" value="{{$_edit == true ? $item->point : ''}}">
            </div>
            <div class="col-1 text-center">/</div>
            <div class="col-5">
              <input type="text" name="max_point" class="form-control" placeholder="" required="true" inputtype="numeric" value="{{$_edit == true ? $item->max_point : '100'}}">
            </div>
          </div>
        </div>
        <div class="col-6 mb-2">
          <label>{{__('labels.average_point')}}</label>
          <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
          <input type="text" name="average_point" class="form-control" placeholder="平均点" inputtype="numeric" value="{{$_edit == true ? $item->average_point : ''}}">
        </div>
        <div class="col-6 mb-2">
          <label>{{__('labels.deviation')}}</label>
          <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
          <input type="text" name="deviation" class="form-control" placeholder="偏差値" inputtype="numeric" value="{{$_edit == true ? $item->deviation : ''}}">
        </div>
        <div class="col-12">
          <div class="form-group">
            <label for="body">
              {{__('labels.file')}}
            </label>
            <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
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
    <div class="row">
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
