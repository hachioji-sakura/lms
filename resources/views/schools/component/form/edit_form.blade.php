<div id="{{$domain}}_edit">
  <form method="POST" action="/{{$domain}}/{{ $item['id'] }}">
    @method('PUT')
    @csrf
    <div class="row">
      <div class="col-12 col-md-6 my-1">
        <label>{{$school_view_entity->localizeName('name')}}</label>
        <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
        <input type="text" class="form-control" name="name" placeholder="{{$school_view_entity->localizeName('name')}}" required="true" value="{{  $item['name'] }}" style="margin-bottom: 15px;">
        <label>{{$school_view_entity->localizeName('name_kana')}}</label>
        <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
        <input type="text" class="form-control" name="name_kana" placeholder="{{$school_view_entity->localizeName('name_kana')}}" required="true" value="{{  $item['name_kana'] }}" style="margin-bottom: 15px;">
        <label>{{$school_view_entity->localizeName('post_number')}}</label>
         <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
        <input type="text" class="form-control" name="post_number" placeholder="{{$school_view_entity->localizeName('post_number')}}" value="{{  $item['phone_number'] }}" style="margin-bottom: 15px;">
        <label>{{$school_view_entity->localizeName('address')}}</label>
         <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
        <input type="text" class="form-control" name="address" placeholder="{{$school_view_entity->localizeName('address')}}" value="{{  $item['address'] }}" style="margin-bottom: 15px;">
        <label>{{$school_view_entity->localizeName('phone_number')}}</label>
         <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
        <input type="text" class="form-control" name="phone_number" placeholder="{{$school_view_entity->localizeName('phone_number')}}" value="{{  $item['phone_number'] }}" style="margin-bottom: 15px;">
        <label>{{$school_view_entity->localizeName('fax_number')}}</label>
         <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
        <input type="text" class="form-control" name="fax_number" placeholder="{{$school_view_entity->localizeName('fax_number')}}" value="{{  $item['fax_number'] }}" style="margin-bottom: 15px;">
      </div>

      <div class="col-12 col-md-6 my-1">
        <label>{{$school_view_entity->localizeName('url')}}</label>
         <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
        <input type="text" class="form-control" name="url" placeholder="{{$school_view_entity->localizeName('url')}}" value="{{  $item['url'] }}" style="margin-bottom: 15px;">
        <label>{{$school_view_entity->localizeName('process')}}</label>
         <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
        <select multiple="" name="process[]" class="form-control" style="margin-bottom: 15px;">
          @foreach($school_view_entity->processList() as $key_name => $process_name)
            @if($item[$key_name] === 1)
              <option value={{ $key_name }} selected>{{ $process_name }}</option>
            @else
              <option value={{ $key_name }}>{{ $process_name }}</option>
            @endif
          @endforeach
        </select>

        <label>{{$school_view_entity->localizeName('department_names')}}</label>
         <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
        <select multiple="" name="department_ids[]" class="form-control" style="margin-bottom: 15px;">
          @foreach($department_list as $id => $department_name)
            @if(in_array($id, $item['department_ids'], true))
              <option value={{ $id }} selected>{{ $department_name }}</option>
            @else
              <option value={{ $id }}>{{ $department_name }}</option>
            @endif
          @endforeach
        </select>

        <label>{{$school_view_entity->localizeName('access')}}</label>
         <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
        <textarea name="access" class="form-control" rows="5">{{ $item['access'] }}</textarea>
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
