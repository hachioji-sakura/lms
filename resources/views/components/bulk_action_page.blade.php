<div id="bulk_action_form">
    <form method="POST" action="{{$action_url}}">
      @csrf
      @method('PUT')
      <div class="row">
        {{$form}}
        <div class="col-6">
          <button type="button" class="btn btn-submit btn-primary w-100" accesskey="bulk_action_form">
            <i class="fa fa-check"></i>
            OK
          </button>
        </div>
        <div class="col-6">
          <a href="javascript:void(0);" data-dismiss="modal" role="button" class="btn btn-secondary btn-block float-left mr-1">
            <i class="fa fa-times-circle mr-1"></i>
            {{__('labels.close_button')}}
          </a>
        </div>
      </div>
    </form>
</div>