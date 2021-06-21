<div id="{{$domain}}_create">
  <form method="POST" action="/{{$domain}}">
    @csrf
    <div class="row">
      <div class="col-12 col-md-6 my-1">
        <div class="form-group">
          <label>学校種別</label>
          <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          <select class="form-control" name="school_type" class="form-control" style="margin-bottom: 15px;" required>
            @foreach($school_types as $school_type => $school_type_name)
                <option value={{ $school_type }}>{{ $school_type_name }}</option>
            @endforeach
          </select>
        </div>
        <label>{{$school_view_entity->localizeName('name')}}</label>
        <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
        <input type="text" class="form-control" name="name" required="true" style="margin-bottom: 15px;">
        <label>{{$school_view_entity->localizeName('name_kana')}}</label>
        <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
        <input type="text" class="form-control" name="name_kana" required="true" style="margin-bottom: 15px;">
        <label>{{$school_view_entity->localizeName('post_number')}}</label>
         <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
        <input type="text" class="form-control" name="post_number" style="margin-bottom: 15px;">
        <label>{{$school_view_entity->localizeName('address')}}</label>
         <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
        <input type="text" class="form-control" name="address" style="margin-bottom: 15px;">
        <label>{{$school_view_entity->localizeName('phone_number')}}</label>
         <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
        <input type="text" class="form-control" name="phone_number" style="margin-bottom: 15px;">
        <label>{{$school_view_entity->localizeName('fax_number')}}</label>
         <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
        <input type="text" class="form-control" name="fax_number" style="margin-bottom: 15px;">
      </div>

      <div class="col-12 col-md-6 my-1">
        <label>{{$school_view_entity->localizeName('url')}}</label>
         <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
        <input type="text" class="form-control" name="url" style="margin-bottom: 15px;">
        <label>{{$school_view_entity->localizeName('process')}}</label>
         <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
        <select multiple="" name="process[]" class="form-control" style="margin-bottom: 15px;">
          @foreach($school_view_entity->processList() as $key_name => $process_name)
              <option value={{ $key_name }}>{{ $process_name }}</option>
          @endforeach
        </select>

        <label>{{$school_view_entity->localizeName('department_names')}}</label>
         <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
        <select multiple="" name="department_ids[]" class="form-control" style="margin-bottom: 15px;">
          @foreach($department_list as $id => $department_name)
              <option value={{ $id }}>{{ $department_name }}</option>
          @endforeach
        </select>
        <label>{{$school_view_entity->localizeName('access')}}</label>
         <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
        <textarea name="access" class="form-control" rows="5"></textarea>
      </div>
    </div>
    <div class="row">
      <div class="col-12 col-md-6 my-1">
        <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="{{$domain}}_create" confirm={{ __('labels.create_confirm') }}>
          <i class="fa fa-edit mr-1"></i>
          {{__('labels.create_button')}}
        </button>
      </div>
      <div class="col-12 col-md-6 my-1">
        <a href="javascript:void(0);" data-dismiss="modal" role="button" class="btn btn-secondary btn-block float-left mr-1">
          <i class="fa fa-times-circle mr-1"></i>
          {{__('labels.cancel')}}
        </a>
      </div>
    </div>
  </form>
</div>
