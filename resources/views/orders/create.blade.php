<div id="create_{{$domain}}">
  @if($_edit)
  <form method="POST" action="/orders/{{$item-id}}">
    @method('PUT')
  @else
  <form method="POST" action="/orders/create">
  @endif
    @csrf
    <div class="row">
      <div class="col-12">
        <label>{{__('labels.title')}}</label>
        <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
        <div class="input-group mb-3">
          <input type="text" name="title" class="form-control" placeholder="{{__('messages.orders_title_placeholder')}}" required="true"  maxlength=50 value="{{$_edit ? $item->title : ''}}">
        </div>
      </div>
      <div class="col-12 mt-2">
        <label>{{__('labels.type')}}</label>
        <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
        <div class="input-group">
          @foreach(config('attribute.order_type') as $key => $val)
          <div class="form-check">
            <label class="form-check-label" for="type_{{$key}}">
              <input class="frm-check-input icheck flat-green" type="radio" name="type" id="type_{{$key}}" value="{{$key}}" required="true" {{$_edit && $item->type == $key ? 'checked': ''}} checked>
              {{$val}}
            </label>
          </div>
          @endforeach
        </div>
      </div>
      <div class="col-12 mt-2">
        <label>{{__('labels.target_user')}}</label>
        <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
        <select name="target_user_id" class="form-control select2" width="100%">
          <option value=" ">{{__('labels.selectable')}}</option>
          @foreach($target_users as $id => $name)
            <option value="{{$user->id}}" {{$_edit && $id == $item->target_user_id ? 'selected ': ''}}>{{$name}}</option>
          @endforeach
        </select>
      </div>
      <div class="col-6 mt-2">
        <label>{{__('labels.amount')}}</label>
        <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
        <div class="input-group mb-3">
          <input type="text" name="amount" class="form-control" placeholder="" required="true" inputtype="numeric" value="{{$_edit ? $item->amount : ''}}">
        </div>
      </div>
      <div class="col-6 mt-2">
        <label>{{__('labels.unit_price')}}</label>
        <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
        <div class="input-group mb-3">
          <input type="text" name="unit_price" class="form-control" placeholder="" required="true" inputtype="numeric" value="{{$_edit ? $item->unit_price : ''}}">
        </div>
      </div>
      <div class="col-12 mt-2">
        <label>{{__('labels.order_item_type')}}</label>
        <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
        <div class="input-group">
          @foreach(config('attribute.order_item_type') as $key => $val)
          <div class="form-check">
            <label class="form-check-label" for="type_{{$key}}">
              <input class="frm-check-input icheck flat-green" type="radio" name="type" id="type_{{$key}}" value="{{$key}}" required="true" {{$_edit && $item->type == $key ? 'checked': ''}} checked>
              {{$val}}
            </label>
          </div>
          @endforeach
        </div>
      </div>
    </div>
    <div class="row mt-2">
      <div class="col-12">
        <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="create_tasks"><i class="fa {{$_edit ? 'fa-edit':'fa-plus-circle'}} mr-1"></i>{{$_edit ? __('labels.update_button') : __('labels.add_button')}}</button>
      </div>
    </div>
  </form>
</div>
