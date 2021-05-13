
  <div id="create_tasks" class="form-group">
    @if($_edit)
    <form method="POST" id="create_task_form" action="/tasks/{{$item->id}}" enctype="multipart/form-data">
      @method('PUT')
    @else
    <form method="POST" action="/tasks" id="create_task_form" enctype="multipart/form-data">
    @endif
      @csrf
      <input type="hidden" name="target_user_id" value="{{$target_student->user_id}}">
      <input type="hidden" name="has_english_lesson" value="{{$has_english_lesson}}">
      <input type="hidden" name="lesson_count" value="{{$lessons->count()}}">
      <input type="hidden" name="grade" value="{{$target_student->get_tag_value('grade')}}">
      <div class="row">
        <div class="col-12">
          <label>
            {{__('labels.type')}}
          </label>
          <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          <div class="input-group">
            <div class="form-check">
              <label class="form-check-label" for="type_class_record">
                <input class="frm-check-input icheck flat-green" type="radio" name="type" id="type_class_record" value="class_record" required="true" {{$_edit && $item->type == 'class_record' ? 'checked': ''}} checked>
                {{__('labels.class_record')}}
              </label>
            </div>
            <div class="form-check">
              <label class="form-check-label" for="type_homework">
                <input class="frm-check-input icheck flat-green" type="radio" name="type" id="type_homework" value="homework" required="true" {{$_edit && $item->type == 'homework' ? 'checked': ''}}>
                {{__('labels.homework')}}
              </label>
            </div>
          </div>
        </div>
      </div>

      @component('tasks.components.subjects', ['_edit' => $_edit, 'subjects' => $subjects, 'domain' => $domain, 'item' => (isset($item) ? $item : null)]) @endcomponent
      <div class="row mt-2">
        <div class="col-12">
          <label>{{__('labels.tasks_remarks')}}</label>
          <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          <div class="input-group mb-3">
            <textarea name="title" id="title" class="form-control" placeholder="{{__('messages.task_body_placeholder')}}" required="true"  maxlength=1000 >{{$_edit ? $item->full_title : ''}}</textarea>
          </div>
        </div>
      </div>
      {{--詳細の役割はtitleが担うのでコメントアウト
      <div class="row mt-2">
        <div class="col-12">
          <label>{{__('labels.tasks_remarks')}}</label>
          <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
          <textarea name="body" class="form-control" placeholder="{{__('messages.task_body_placeholder')}}" >{{$_edit ? $item->body : ''}}</textarea>
        </div>
      </div>
      --}}
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
          <div class="col-12 col-md-6">
            <label>{{__('labels.milestones')}}</label>
            <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
            <select name="milestone_id" class="form-control select2" width="100%">
              <option value=" ">{{__('labels.selectable')}}</option>
              @foreach($target_student->target_milestone as $milestone)
                <option value="{{$milestone->id}}" {{$_edit && $milestone->id == $item->milestone_id ? 'selected ': ''}}>{{$milestone->title}}</option>
              @endforeach
            </select>
          </div>

          <div class="col-12 col-md-6">
            <label>{{__('labels.textbooks')}}</label>
            <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
            <select name="textbook_ids[]" class="form-control select2" width="100%" multiple="multiple" >
              @foreach($textbooks as $textbook)
                <option value="{{$textbook->id}}" {{$_edit && in_array($textbook->id,$item->textbooks->pluck('id')->toArray()) ? 'selected ': ''}}>{{$textbook->name}}</option>
              @endforeach
            </select>
          </div>

          @if($_edit)
          <div class="col-12 col-md-6">
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
          <div class="col-6">
            <label>{{__('labels.start_schedule')}}</label>
            <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
            <input type="text" name="start_schedule" class="form-control" uitype="datepicker" minvalue="{{date('Y/m/d')}}"   placeholder=""  value="{{$_edit && !empty($item->start_schedule) ? date("Y/m/d", strtotime($item->start_schedule)) : ''}}">
          </div>
          <div class="col-6">
            <label>{{__('labels.end_schedule')}}</label>
            <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
            <input type="text" name="end_schedule" class="form-control" uitype="datepicker" minvalue="{{date('Y/m/d')}}" placeholder=""  value="{{$_edit && !empty($item->end_schedule) ?  date("Y/m/d", strtotime($item->end_schedule)) : "" }}">
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
              <label class="form-check-label" for="mail_send_yes">
              <input class="frm-check-input icheck flat-green" type="radio" name="mail_send" id="mail_send_yes" value="yes" required="true">
                {{__('labels.task_remind')}}
              </label>
            </div>
            <div class="form-check">
              <label class="form-check-label" for="mail_send_no">
              <input class="frm-check-input icheck flat-green" type="radio" name="mail_send" id="mail_send_no" value="no" required="true" checked>
                {{__('labels.no_remind')}}
              </label>
            </div>
          </div>
        </div>
      </div>
      @endif
      <div class="row mt-2">
        <div class="col-12">
          <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="create_tasks"><i class="fa {{$_edit ? 'fa-edit':'fa-plus-circle'}} mr-1"></i>{{$_edit ? __('labels.update_button') : __('labels.add_button')}}</button>
        </div>
      </div>
    </form>
  </div>
  <script>

  $(function(){
    $('#clear_title').on('click',function(e){
      $('#title').val("");
    });
  });
  </script>
