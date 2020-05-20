
  @if($item->status == "new" )
    <form method="POST" action="/tasks/{{$item->id}}/progress" class="float-left mr-1">
      @csrf
      @method('PUT')
      <button type="submit" class="btn btn-sm btn-submit btn-outline-{{config('status_style')['progress']}} rounded-pill mr-1 mb-1">
        {{__('labels.progress_button')}}
      </button>
    </form>
  @endif
  @if($item->status == "progress")
    <form method="POST" action="/tasks/{{$item->id}}/done" class="float-left mr-1">
      @csrf
      @method('PUT')
      <button type="submit" class="btn btn-sm btn-block btn-submit btn-outline-{{config('status_style')['done']}} mr-1 mb-1">
        {{__('labels.done_button')}}
      </button>
    </form>
  @endif
  @if($item->status == "done")
    <a href="javascript:void(0)" title="{{__('labels.review_button')}}" page_form="dialog" page_title="{{$item->title}}" page_url="/tasks/{{$item->id}}/review" class="btn btn-sm btn-outline-{{$is_footer ? 'app' : config('status_style')['complete']}} mr-1 mb-1" role="button">
    {{__('labels.review_button')}}
    </a>
  @endif
