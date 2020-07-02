<div class="row">
  <div class="col-12">
    @if(!empty($item->subjects))
      @foreach($item->subjects as $subject)
      <small class="badge badge-primary">
        {{$subject->name}}
      </small>
      @endforeach
    @endif
  </div>
  <div class="col-12 text-truncate">
    <a href="javascript:void(0)" title="{{__('labels.details')}}" page_form="dialog" page_title="{{$item->name}}" page_url="/{{$domain}}/{{$item->id}}" role="button">
      {{$item->name}}
    </a>
  </div>
  <div class="col-12 col-md-8 text-truncate">
    <small class="text-muted">
      {{$item->remarks}}
    </small>
  </div>
</div>
