
  @if($item->status == "progress")
  <dt>
    <a href="javascript:void(0)" title="{{__('labels.new_button')}}" page_form="dialog" page_title="{{__('messages.task_confirm')}}" page_url="/tasks/{{$item->id}}/new" class="btn btn-sm btn-{{$is_footer ? 'app' : config('status_style')['new']}} mr-1" role="button">
    <i class="fa fa-plus"></i>
    {{__('labels.new_button')}}
    </a>
  </dt>
  @endif
  @if($item->status == "new" || $item->status == "done")
  <dt>
    <a href="javascript:void(0)" title="{{__('labels.progress_button')}}" page_form="dialog" page_title="{{__('messages.task_confirm')}}" page_url="/tasks/{{$item->id}}/progress" class="btn btn-sm btn-{{$is_footer ? 'app' : config('status_style')['progress']}} mr-1" role="button">
    <i class="fa fa-play"></i>
    {{__('labels.progress_button')}}
    </a>
  </dt>
  @endif
  @if($item->status == "progress" || $item->status == "complete")
  <dt>
    <a href="javascript:void(0)" title="{{__('labels.done_button')}}" page_form="dialog" page_title="{{__('messages.task_confirm')}}" page_url="/tasks/{{$item->id}}/done" class="btn btn-sm btn-{{$is_footer ? 'app' : config('status_style')['done']}} mr-1" role="button">
    <i class="fa fa-stop"></i>
    {{__('labels.done_button')}}
    </a>
  </dt>
  @endif
  @if($item->status == "done")
  <dt>
    <a href="javascript:void(0)" title="{{__('labels.review_button')}}" page_form="dialog" page_title="{{__('messages.task_confirm')}}" page_url="/tasks/{{$item->id}}/complete" class="btn btn-sm btn-{{$is_footer ? 'app' : config('status_style')['complete']}} mr-1" role="button">
    <i class="fa fa-pen"></i>
    {{__('labels.review_button')}}
    </a>
  </dt>
  @endif
  @if($item->status != "cancel")
  <dt>
    <a href="javascript:void(0)" title="{{__('labels.cancel')}}" page_form="dialog" page_title="{{__('messages.task_confirm')}}" page_url="/tasks/{{$item->id}}/cancel" class="btn btn-sm btn-{{$is_footer ? 'app' : config('status_style')['cancel']}} mr-1" role="button">
    <i class="fa fa-ban"></i>
     {{__('labels.cancel')}}
    </a>
  </dt>
  @endif
