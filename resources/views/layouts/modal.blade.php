@section('modal')
{{-- <div class="modal fade" id="subDialog" tabindex="-1"> --}}
<div class="modal fade" id="subDialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">
          <i class="fa fa-times"></i>
        </button>
        <h4 class="modal-title content-sub-title page_title"></h4>
      </div>
      <div class="modal-body content-sub-body page_contents">
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="loading" tabindex="-1" style="z-index:9999;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body content-sub-body page_contents">
        <i class="fa fa-spinner fa-spin mr-1"></i>
        データロード中
      </div>
    </div>
  </div>
</div>

@endsection
