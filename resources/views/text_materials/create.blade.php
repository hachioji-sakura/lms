
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
    <div class="row">
      <div class="col-8">
        <div class="form-group">
          <label for="name" class="w-100">
            教材名
            <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          </label>
          <input type="text" name="name" class="form-control" required="true" maxlength=20
          @if(isset($_edit) && $_edit==true)
           value="{{$item->name}}" placeholder="(変更前) {{$item->name}}">
          @else
           placeholder="ex.足し算/練習問題">
          @endif
        </div>
      </div>
      <div class="col-4">
        <label for="title" class="w-100">
          {{__('labels.to_public')}}
        </label>
        <label class="mx-2">
          <input type="checkbox" value="1" name="is_public" class="icheck flat-red"
          @if($_edit==true && $item->is_publiced()==true)
          checked
          @endif
          >{{__('labels.public')}}
        </label>
      </div>
      <div class="col-12">
        <div class="form-group">
          <label for="body" class="w-100">
            {{__('labels.description')}}
            <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
          </label>
          <textarea type="text" name="description" class="form-control"  maxlength=100
          @if(isset($_edit) && $_edit==true)
            placeholder="(変更前) {{$item->description}}" >{{$item->description}}</textarea>
          @else
            placeholder="100文字まで" ></textarea>
          @endif
        </div>
      </div>
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
      @if(empty($target_user_id))
      <div class="col-12">
        <div class="form-group">
          <label for="title" class="w-100">
            対象
            {{__('labels.teachers')}}
          </label>
          <select name="target_user_id" class="form-control select2"  required="true" width=100%  >
            <option value="">{{__('labels.selectable')}}</option>
            @foreach($teachers as $teacher)
               <option
               value="{{ $teacher->user_id }}"
               @if(!empty($item))
                 {{ $item->target_user_id=$teacher->user_id  ? "selected" : "" }}
               @endif
               >{{$teacher->name()}}</option>
            @endforeach
          </select>
        </div>
      </div>
      @elseif(isset($item))
      <div class="col-12">
        <div class="form-group">
          <label for="charge_user" class="w-100">
            {{__('labels.charge_user')}}
          </label>
          {{$item->target_user->teacher->name()}}
        </div>
      </div>
      @endif
    </div>
    @component('text_materials.forms.subjects', ['_edit' => $_edit, 'subjects' => $subjects, 'domain' => $domain,  'item' => (isset($item) ? $item : null)]) @endcomponent

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
