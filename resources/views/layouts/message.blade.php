@section('message')
<div class="modal fade" id="message" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">
          <i class="fa fa-times"></i>
        </button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" accesskey="ok">
          <i class="fa fa-check"></i>
          <span class="ml-1">OK</span>
        </button>
        <button type="button" class="btn btn-primary" accesskey="yes">
          <i class="fa fa-check"></i>
          <span class="ml-1">はい</span>
        </button>
        <button type="button" class="btn btn-secondary" accesskey="no">
          <i class="fa fa-times"></i>
          <span class="ml-1">いいえ</span>
        </button>
      </div>
    </div>
  </div>
</div>
@endsection
