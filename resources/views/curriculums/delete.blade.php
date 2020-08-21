<div id="delete_form">
  <div class="row">
    <div class="col-6">
      <form method="POST" action="/{{$domain}}/{{$item->id}}">
        @csrf
        @method('DELETE')
        <button type="button" class="btn btn-submit btn-primary w-100" accesskey="delete_form">
          <i class="fa fa-trash"></i>
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
