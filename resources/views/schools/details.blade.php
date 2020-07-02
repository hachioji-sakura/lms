<div class="">
  <div class="row">
    <div class="col-6">
      <label>{{__('labels.school_name')}}</label>
      <div class="form-group">
        {{$item->name}}
      </div>
    </div>
    <div class="col-6">
      <label>{{__('labels.create_user')}}</label>
      <div class="form-group">
        {{$item->create_user->details()->name()}}
      </div>
    </div>
  </div>
  @if(isset($item->remarks))
  <div class="row">
    <div class="col-12">
      <label>{{__('labels.remark')}}</label>
      <div class="form-group">
          {!!nl2br($item->remarks)!!}
      </div>
    </div>
  </div>
  @endif
  @if(isset($item->hp_url))
  <div class="row">
    <div class="col-12">
      <label>{{__('labels.hp_url')}}</label>
      <div class="form-group">
        <a href="https://{{$item->hp_url}}" target="_blank">
          {!!nl2br($item->hp_url)!!}
        </a>
      </div>
    </div>
  </div>
  @endif
</div>
<div class="row mt-3">
  <div class="col-12">
    <button type="reset" class="btn btn-sm btn-secondary btn-block">
      <i class="fa fa-times mr-1"></i>
      {{__('labels.close_button')}}
    </button>
  </div>
</div>
