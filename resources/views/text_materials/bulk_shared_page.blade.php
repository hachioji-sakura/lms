@component('components.bulk_action_page',['action_url' => $action_url])
    @slot('form')
        @if ($items->count() > 0)
            <div class="col-12">
                <label>{{__('labels.share')}}{{__('labels.text_materials')}}</label>
            </div>
            @foreach ($items as $item)
            <div class="col-12 mb-2">
                {{$item->name}}:{{$item->target_user->details()->name}}
                <input type="hidden" name="text_material_ids[]" value="{{$item->id}}">
            </div>
            @endforeach
          <div class="col-12 mb-2">
            <label>{{__('labels.share')}}{{__('labels.teachers')}}</label>
            <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
            <select name="shared_user_ids[]" class="form-control select2" width="100%" multiple="multiple">
              <option value=" ">{{__('labels.selectable')}}</option>
              @foreach($shared_users as $user)
                <option value="{{$user->id}}">{{$user->details()->name}}</option>
              @endforeach
            </select>
          </div>
          <div class="col-12 mb-2">
            <label>{{__('labels.share')}}{{__('labels.type')}}</label>
            <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
            <div class="form-check">
              <label class="form-check-label mr-2">
                <input type="radio" class="form-check-input icheck flat-green" name="method" id="" value="sync" required=true>
                {{__('labels.share')}}{{__('labels.teachers')}}{{__('labels.edit')}}
              </label>
              <label class="form-check-label">
                <input type="radio" class="form-check-input icheck flat-green" name="method" id="" value="attach" required=true>
                {{__('labels.share')}}{{__('labels.teachers')}}{{__('labels.add')}}
              </label>
            </div>
          </div>

        @endif
    @endslot
@endcomponent