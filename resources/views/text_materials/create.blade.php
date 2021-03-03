<div id="{{$domain}}_create">
  @if(isset($_edit) && $_edit==true)
  <form id="edit" method="POST" action="/{{$domain}}/{{$item['id']}}" enctype="multipart/form-data">
    @method('PUT')
  @else
  <form id="edit" method="POST" action="/{{$domain}}" enctype="multipart/form-data">
  @endif
  @csrf
  <input type="text" name="dummy" style="display:none;" / >
  <input type="hidden" name="target_user_id" value="{{$target_user_id}}">
  <input type="hidden" name="lesson_count" value="{{$lessons->count()}}">
  <input type="hidden" name="has_english_lesson" value="{{$has_english_lesson}}">

  @component('tasks.components.subjects', ['_edit' => $_edit, 'subjects' => $subjects, 'item' => (isset($item) ? $item : null)]) @endcomponent

    <div class="row">
      <div class="col-12">
        <div class="form-group">
          <label for="body" class="w-100">
            アップロード
            <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          </label>
          @if(isset($_edit) && $_edit == true && !empty($item['s3_url']))
          <label for="upload_file" class="w-100 upload_file">
            <a id="upload_file_link" href="{{$item['s3_url']}}" target="_blank" class="">{{$item['name']}}</a>
            <a href="javascript:void(0);" onClick="upload_file_clear();"class="btn btn-default btn-sm ml-1">
              <i class="fa fa-times"></i>
            </a>
          </label>
          <input type="hidden" name="upload_file_delete" value="0">
          <input type="hidden" name="upload_file_name" value="{{$item['s3_alias']}}">
          <script>
          function upload_file_clear(){
            console.log("update_file_clear");
            $(".upload_file").hide();
            $("input[name='upload_file_delete']").val(1);
            $("input[name='upload_file']").collapse('show');
          }
          </script>
          @endif
          <input type="file" name="upload_file" class="form-control
          @if(isset($_edit) && $_edit == true && !empty($item['s3_url']))]
           collapse
          @endif
          " placeholder="ファイル" required="true" maxlength=200>
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
        <div class="form-group">
          <label for="body" class="w-100">
            説明
            <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
          </label>
          <textarea type="text" name="description" class="form-control"  maxlength=1000
          @if(isset($_edit) && $_edit==true)
            placeholder="(変更前) {{$item->description}}" >{{$item->description}}</textarea>
          @else
            placeholder="1000文字まで" ></textarea>
          @endif
        </div>
      </div>
    </div>
    <div class="col-12">
      <div class="form-group">
        <label for="publiced_at" class="w-100">
          {{__('labels.publiced_at')}}
          <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
        </label>
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
          </div>
          <input type="text" id="publiced_at" name="publiced_at" class="form-control float-left" required="true" uitype="datepicker" placeholder="例：{{date('Y/m/d')}}"
          @if(isset($_edit) && $_edit==true && isset($item) && isset($item['publiced_at']) && $item['publiced_at']!='9999-12-31')
            value="{{date('Y/m/d', strtotime($item['publiced_at']))}}"
          @elseif(isset($item) && isset($item['publiced_at']) && $item['publiced_at']!='9999-12-31')
            value="{{date('Y/m/d', strtotime($item['publiced_at']))}}"
          @else
            value = "{{date('Y/m/d')}}"
          @endif
          @if(!(isset($_edit) && $_edit==true))
          minvalue="{{date('Y/m/d')}}"
          @endif
          >
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12 col-md-6 mb-1">
          <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="{{$domain}}_create">
            @if(isset($_edit) && $_edit==true)
              {{__('labels.update_button')}}
            @else
              {{__('labels.add_button')}}
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
            {{__('labels.cancel_button')}}
          </button>
      </div>
    </div>
  </form>
</div>
