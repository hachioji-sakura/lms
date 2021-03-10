<div id="{{$domain}}_delete">
  <form method="POST" action="/{{$domain}}/delete/{{ $id }}">
    @csrf
    <input type="text" name="dummy" style="display:none;"/>
    <div class="row">
      <div class="col-12 col-md-6 my-1">
        <button type="button" class="btn btn-submit btn-danger btn-block" accesskey="{{$domain}}_delete" confirm="{{__('labels.delete_confirm')}}">
          <i class="fa fa-trash mr-1"></i>
          {{__('labels.delete')}}
        </button>
      </div>
      <div class="col-12 col-md-6 my-1">
        <a href="javascript:void(0);" data-dismiss="modal" role="button" class="btn btn-secondary btn-block float-left mr-1">
          <i class="fa fa-times-circle mr-1"></i>
          {{__('labels.cancel')}}
        </a>
      </div>
    </div>
  </form>
</div>
