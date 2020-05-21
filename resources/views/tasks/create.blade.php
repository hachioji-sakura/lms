
  <div id="create_tasks" class="form-group">
    @if($_edit)
    <form method="POST"  action="/tasks/{{$item->id}}" enctype="multipart/form-data">
      @method('PUT')
    @else
    <form method="POST" action="/tasks" enctype="multipart/form-data">
    @endif
      @csrf
      <input type="hidden" name="target_user_id" value="{{$target_student->user_id}}">
      <div class="row mt-2">
        <div class="col-12">
          <label>{{__('labels.title')}}</label>
          <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          <input type="text" class="form-control" name="title" placeholder="{{__('labels.title')}}" required="true" value="{{$_edit ? $item->title : ''}}">
        </div>
      </div>

      <div class="row mt-2">
        <div class="col-12">
          <label>{{__('labels.type')}}</label>
          <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          <select name="type" class="form-control"  required="true">
            @foreach(config('attribute.task_type') as $key => $value)
            <option value="{{$key}}"
            @if(!empty($item) && $_edit)
              {{$item->type == $key ? "selected" : "" }}
            @endif
            >{{$value}}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="row mt-2">
        <div class="col-12">
          <h3 class="card-title">
            <label>
            {{__('labels.setting_details')}}
            </label>
             <button type="button" class="btn btn-tool" data-toggle="collapse" data-target="#setting_details"><i class="fas fa-plus"></i></button>
          </h3>
        </div>
      </div>
      <div class="collapse" id="setting_details">
        <div class="row mt-2 collpase" id="setting_details">
          <div class="col-6">
            <label>{{__('labels.milestones')}}</label>
            <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
            <select name="milestone_id" class="form-control select2" width="100%">
              <option value=" ">{{__('labels.selectable')}}</option>
              @foreach($target_student->target_milestone as $milestone)
                <option value="{{$milestone->id}}" {{$_edit && $milestone->id == $item->milestone_id ? 'selected ': ''}}>{{$milestone->title}}</option>
              @endforeach
            </select>
          </div>
          @if($_edit)
          <div class="col-6">
            <label>{{__('labels.status')}}</label>
            <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
            <select name="status" class="form-control select2" width="100%">
              @foreach(config('attribute.task_status') as $key => $value)
                <option value="{{$key}}" {{$_edit && $key == $item->status ? 'selected ': ''}}>{{$value}}</option>
              @endforeach
            </select>
          </div>
          @endif
        </div>

        <div class="row mt-2">
          <div class="col-12">
            <label>{{__('labels.tasks_remarks')}}</label>
            <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
            <textarea name="body" class="form-control" placeholder="{{__('labels.tasks_remarks')}}" >{{$_edit ? $item->body : ''}}</textarea>
          </div>
        </div>
        <div class="row mt-2">
          <div class="col-6">
            <label>{{__('labels.start_schedule')}}</label>
            <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
            <input type="text" name="start_schedule" class="form-control" uitype="datepicker" minvalue="{{date('Y/m/d')}}"   placeholder=""  value="{{$_edit ? $item->start_schedule : ""}}">
          </div>
          <div class="col-6">
            <label>{{__('labels.end_schedule')}}</label>
            <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
            <input type="text" name="end_schedule" class="form-control" uitype="datepicker" minvalue="{{date('Y/m/d')}}" placeholder=""  value="{{$_edit ? $item->end_schedule : "" }}">
          </div>
        </div>
        <div class="row mt-2">
          <div class="col-12">
            @if(($_edit) && !empty($item['s3_url']))
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
            <input type="file" name="upload_file" class="form-control">
          </div>
        </div>
      </div>
      @if($_edit)
      <input type="hidden" name="mail_send" value="false">
      @else
      <div class="row mt-2">
        <div class="col-12">
          <label>
            {{__('labels.notification')}}
          </label>
          <div class="input-group">
            <div class="form-check">
              <input class="frm-check-input icheck flat-green" type="radio" name="mail_send" id="mail_send_yes" value="yes" required="true">
              <label class="form-check-label" for="mail_send_yes">
                {{__('labels.task_remind')}}
              </label>
            </div>
            <div class="form-check">
              <input class="frm-check-input icheck flat-green" type="radio" name="mail_send" id="mail_send_no" value="no" required="true" checked>
              <label class="form-check-label" for="mail_send_no">
                {{__('labels.no_remind')}}
              </label>
            </div>
          </div>
        </div>
      </div>
      @endif
      <div class="row mt-2">
        <div class="col-12">
          <button type="button" class="btn btn-submit btn-primary btn-block accesskey="create_tasks""><i class="fa {{$_edit ? 'fa-edit':'fa-plus-circle'}} mr-1"></i>{{$_edit ? __('labels.update_button') : __('labels.add_button')}}</button>
        </div>
      </div>
    </form>
  </div>


<script>
$(function(){
  base.pageSettinged("create_tasks",null);
  $('button.btn-submit').on('click', function(e){
    e.preventDefault
    if(front.validateFormValue('create_tasks')){
      console.log('hoge');
      $("form").submit();
    }
  });
});
</script>
