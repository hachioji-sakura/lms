<div class="">
  <div class="row">
    <div class="col-6">
      <label>{{__('labels.'.$domain.'_name')}}</label>
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
  @if($domain == "curriculums")
  <div class="row mt-1">
    <div class="col-12">
      <label>{{__('labels.subject')}}</label>
      <div class="form-group">
      @foreach($item->subjects as $subject)
      <small class="badge badge-primary">
        {{$subject->name}}
      </small>
      @endforeach
      </div>
    </div>
  </div>
  @endif
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
</div>
<div class="row mt-3">
  <div class="col-12">
    <button type="reset" class="btn btn-sm btn-secondary btn-block">
      <i class="fa fa-times mr-1"></i>
      {{__('labels.close_button')}}
    </button>
  </div>
</div>
