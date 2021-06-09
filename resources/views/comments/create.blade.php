<div id="{{$domain}}_create">
  @if(isset($_edit) && $_edit==true)
  <form id="edit" method="POST" action="/{{$domain}}/{{$item['id']}}" enctype="multipart/form-data">
    @method('PUT')
  @else
  <form id="edit" method="POST" action="/{{$domain}}" enctype="multipart/form-data">
  @endif
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
  @if(isset($origin))
    <input type="hidden" value="{{$origin}}" name="origin" />
  @endif
  @if(isset($item_id))
    <input type="hidden" value="{{$item_id}}" name="item_id" />
  @endif
  @if(isset($student_id))
    <input type="hidden" value="{{$student_id}}" name="student_id" />
  @endif
  @if(isset($teacher_id))
    <input type="hidden" value="{{$teacher_id}}" name="teacher_id" />
  @endif
  @if(isset($manager_id))
    <input type="hidden" value="{{$manager_id}}" name="manager_id" />
  @endif
  @if(isset($is_memo) && $is_memo == true)
  <input type="hidden" name="type" value="memo">
  @else
    <div class="row">
      <div class="col-12">
        <div class="form-group">
          <label for="title" class="w-100">
            種別
            <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          </label>
          <select name="type" class="form-control" placeholder="種別" required="true">
            @foreach($attributes['comment_type'] as $index => $name)
               <option value="{{ $index }}"
               @if(isset($_edit)  && $_edit==true && $item['type'] == $index)
               selected
               @endif
               >{{$name}}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
    @endif  
    {{--
    <div class="row">
      <div class="col-12">
        <div class="form-group">
          <label for="title" class="w-100">
            概要
            <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          </label>
          <input type="text" id="title" name="title" class="form-control" required="true" maxlength=50
          @if(isset($_edit) && $_edit==true)
           value="{{$item['title']}}" placeholder="(変更前) {{$item['title']}}">
          @else
           placeholder="50文字まで">
          @endif
        </div>
      </div>
    </div>
    --}}
    <input type="hidden" id="title" name="title" value="-">
    <div class="row">
      <div class="col-12">
        <div class="form-group">
          <label for="body" class="w-100">
            内容
            <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          </label>
          <textarea type="text" id="body" name="body" class="form-control" required="true"  maxlength=500
          @if(isset($_edit) && $_edit==true)
            placeholder="(変更前) {{$item['body']}}" >{{$item['body']}}</textarea>
          @else
            placeholder="500文字まで" ></textarea>
          @endif
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <div class="form-group">
          <label for="body" class="w-100">
            アップロード
            <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
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
          function upload_file_clear(){
            console.log("update_file_clear");
            $(".upload_file").hide();
            $("input[name='upload_file_delete']").val(1);
          }
          </script>
          @endif
          <input type="file" name="upload_file" class="form-control" placeholder="ファイル">
          @if ($errors->has('upload_file'))
          <span class="invalid-feedback">
          <strong>{{ $errors->first('upload_file') }}</strong>
          </span>
          @endif
        </div>
      </div>
    </div>
    @if(isset($_edit)  && $_edit==true && ($user->role=='manager' || $user->role=='teacher'))
    <div class="row">
      <div class="col-12">
        <div class="form-group">
          <label for="title" class="w-100">
            重要度
            <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          </label>
          <select name="importance" class="form-control" placeholder="重要度" required="true">
            @foreach($attributes['importance'] as $index => $name)
               <option value="{{ $index }}"
               @if(isset($_edit)  && $_edit==true && $item['importance'] == $index)
               selected
               @endif
               >{{$name}}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
    @endif
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
<script>
$(function(){
  base.pageSettinged('{{$domain}}_create', null);
});
</script>
