<div id="cancel_form">
  <div class="row">
    <div class="col-6">
      <form method="POST" action="/tasks/{{$item->id}}/cancel">
        @csrf
        @method('PUT')
        <button type="submit" class="btn btn-submit btn-primary w-100">
          <i class="fa fa-trash"></i>
          {{__('labels.cancel_button')}}
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
