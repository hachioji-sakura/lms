
  <div id="create_curriculums" class="form-group">
    @if($_edit)
    <form method="POST" id="create_task_form" action="/curriculums/{{$item->id}}" enctype="multipart/form-data">
      @method('PUT')
    @else
    <form method="POST" action="/curriculums" id="create_task_form" enctype="multipart/form-data">
    @endif
      @csrf
      <div class="row mt-2">
        <div class="col-12">
          <label>{{__('labels.name')}}</label>
          <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          <input type="text" class="form-control" name="title" placeholder="{{__('labels.name')}}" required="true" value="{{$_edit ? $item->name : ''}}">
        </div>
      </div>

      <div class="row mt-2">
        <div class="col-12">
          <label>{{__('labels.subject')}}</label>
          <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          <select name="subjects[]" class="form-control select2" width="100%" multiple="multiple" required="true">
            @foreach($subjects as $subject)
            <option value="{{$subject->id}}"
            @if(!empty($item) && $_edit)
              {{$item->type == $subject->id ? "selected" : "" }}
            @endif
            >{{$subject->attribute_name}}</option>
            @endforeach
          </select>
          <script>
          $(".select2").select2({
            if ($(data).filter(function() {
                return this.text.localeCompare(term)===0; }).length===0) {
                return { id:term, text:term };
              }
          });
          </script>
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

      <div class="row mt-2">
        <div class="col-12">
          <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="create_curriculums"><i class="fa {{$_edit ? 'fa-edit':'fa-plus-circle'}} mr-1"></i>{{$_edit ? __('labels.update_button') : __('labels.add_button')}}</button>
        </div>
      </div>
    </form>
  </div>
