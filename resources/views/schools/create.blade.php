
  <div id="create_schools" class="form-group">
    @if($_edit)
    <form method="POST" id="create_{{$domain}}_form" action="/{{$domain}}/{{$item->id}}" enctype="multipart/form-data">
      @method('PUT')
    @else
    <form method="POST" action="/{{$domain}}" id="create_{{$domain}}_form" enctype="multipart/form-data">
    @endif
      @csrf
      <div class="row mt-2">
        <div class="col-12">
          <label>{{__('labels.school_name')}}</label>
          <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          <input type="text" class="form-control" name="name" placeholder="{{__('labels.school_name')}}" required="true" value="{{$_edit ? $item->name : ''}}">
        </div>
      </div>

      <div class="row mt-2">
        <div class="col-12">
          <label>{{__('labels.remark')}}</label>
          <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
          <textarea name="remarks" class="form-control" placeholder="{{__('labels.remark')}}" >{{$_edit ? $item->remarks : ''}}</textarea>
        </div>
      </div>

      <div class="row mt-2">
        <div class="col-12">
          <label>{{__('labels.hp_url')}}</label>
          <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
          <input type="text" class="form-control" name="hp_url" placeholder="{{__('labels.schools_hp_url')}}" value="{{$_edit ? $item->hp_url : ''}}">
        </div>
      </div>

      <div class="row mt-2">
        <div class="col-12">
          <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="create_schools"><i class="fa {{$_edit ? 'fa-edit':'fa-plus-circle'}} mr-1"></i>{{$_edit ? __('labels.update_button') : __('labels.add_button')}}</button>
        </div>
      </div>
    </form>
  </div>
