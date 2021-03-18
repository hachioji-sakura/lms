<div id="{{$domain}}_edit">
  <form method="POST" action="/{{$domain}}/{{ $high_school_entity->highSchoolId() }}">
    @method('PUT')
    @csrf
    <div class="row">
      <div class="col-12 col-md-6 my-1">
        <h6>{{ $school_view_entity->localizeName('name') }}</h6>
        <p><input id="text" name="name" type="text" class="form-control" value={{ $high_school_entity->name() }} required></p>
        <h6>{{ $school_view_entity->localizeName('name_kana') }}</h6>
        <p><input id="text" name="name_kana" type="text" class="form-control" value={{ $high_school_entity->nameKana() }} required></p>
        <h6>{{ $school_view_entity->localizeName('post_number') }}</h6>
        <p><input id="text" name="post_number" type="text" class="form-control" value={{ $high_school_entity->postNumber() }}></p>
        <h6>{{ $school_view_entity->localizeName('address') }}</h6>
        <p><input id="text" name="address" type="text" class="form-control" value={{ $high_school_entity->address() }}></p>
        <h6>{{ $school_view_entity->localizeName('phone_number') }}</h6>
        <p><input id="text" name="phone_number" type="text" class="form-control" value={{ $high_school_entity->phoneNumber() }}></p>
        <h6>{{ $school_view_entity->localizeName('fax_number') }}</h6>
        <p><input id="text" name="fax_number" type="text" class="form-control" value={{ $high_school_entity->faxNumber() }}></p>
        <h6>{{ $school_view_entity->localizeName('url') }}</h6>
        <p><input id="text" name="url" type="text" class="form-control" value={{ $high_school_entity->url() }}></p>
      </div>
      <div class="col-12 col-md-6 my-1">
        <h6>{{ $school_view_entity->localizeName('process') }}</h6>
        <select multiple="" name="process[]" class="form-control" style="margin-bottom: 15px;">
          @foreach($school_view_entity->processList() as $key_name => $process_name)
            @if($high_school_entity->{$key_name}() === true)
              <option value={{ $key_name }} selected>{{ $process_name }}</option>
            @else
              <option value={{ $key_name }}>{{ $process_name }}</option>
            @endif
          @endforeach
        </select>

        <h6>{{ $school_view_entity->localizeName('department_names') }}</h6>
        <select multiple="" name="department_ids[]" class="form-control" style="margin-bottom: 15px;">
          @foreach($department_list as $id => $department_name)
            @if(in_array($id, $high_school_entity->departmentIds(), true))
              <option value={{ $id }} selected>{{ $department_name }}</option>
            @else
              <option value={{ $id }}>{{ $department_name }}</option>
            @endif
          @endforeach
        </select>
        <h6>{{ $school_view_entity->localizeName('access') }}</h6>
        <textarea name="access" class="form-control" rows="5">{{ $high_school_entity->access() }}</textarea>
      </div>
    </div>
    <div class="row">
      <div class="col-12 col-md-6 my-1">
        <button type="button" class="btn btn-submit btn-success btn-block" accesskey="{{$domain}}_edit" confirm="変更しますか？">
          <i class="fa fa-edit mr-1"></i>
          {{__('labels.edit')}}
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
