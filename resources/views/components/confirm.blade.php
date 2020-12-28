<div id="{{$action}}_form">
  <div class="row">
    @if(isset($message))
    <div class="col-12 mb-2 bg-warning p-4">
      <i class="fa fa-exclamation-triangle mr-2"></i>
      {!!$message!!}
    </div>
    @endif
    <div class="col-6">
      <form method="POST" action="/{{$domain}}/{{$item->id}}/{{$action}}">
        @csrf
        @method('PUT')
        <button type="button" class="btn btn-submit btn-primary w-100" accesskey="{{$action}}_form">
          <i class="fa fa-check"></i>
          OK
        </button>
      </form>
    </div>
    <div class="col-6">
      <a href="javascript:void(0);" data-dismiss="modal" role="button" class="btn btn-secondary btn-block float-left mr-1">
        <i class="fa fa-times-circle mr-1"></i>
        {{__('labels.back_button')}}
      </a>
    </div>
  </div>
</div>
