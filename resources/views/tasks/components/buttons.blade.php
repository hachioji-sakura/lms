
  @if($item->status == "new" )
  <div id="task_progress">
    <form method="POST" action="/tasks/{{$item->id}}/progress" class="float-left mr-1">
      @csrf
      @method('PUT')
      <button type="submit" class="btn btn-sm btn-submit btn-outline-{{config('status_style')['progress']}} rounded-pill mr-1 mb-1" accesskey="task_progress">
        {{__('labels.progress_button')}}
      </button>
    </form>
  </div>
  <script>
  $(function(){
    base.pageSettinged("task_progress", null);
    //submit
    $("#task_progress button.btn-submit").on('click', function(e){
      $(this).prop("disabled",true);
      $("#task_progress form").submit();
    });
  });
  </script>

  @endif
  @if($item->status == "progress")
  <div id="task_done">
    <form method="POST" action="/tasks/{{$item->id}}/done" class="float-left mr-1">
      @csrf
      @method('PUT')
      <button type="submit" class="btn btn-sm btn-block btn-submit btn-outline-{{config('status_style')['done']}} mr-1 mb-1" accesskey="task_done">
        {{__('labels.done_button')}}
      </button>
    </form>
  </div>
  <script>
  $(function(){
    base.pageSettinged("task_done", null);
    //submit
    $("#task_done button.btn-submit").on('click', function(e){
      $(this).prop("disabled",true);
      $("#task_done form").submit();
    });
  });
  </script>
  @endif
  @if($item->status == "done")
    <a href="javascript:void(0)" title="{{__('labels.review_button')}}" page_form="dialog" page_title="{{$item->title}}" page_url="/task_reviews/create?task_id={{$item->id}}" class="btn btn-sm btn-outline-{{$is_footer ? 'app' : config('status_style')['complete']}} mr-1 mb-1" role="button">
    {{__('labels.review_button')}}
    </a>
  @endif
