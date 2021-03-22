<div id="agreement_correction">
  @if(isset($_edit) && $_edit == true)
  <form method="POST"  action="/agreements/{{$item->id}}">
    @csrf
    @method('PUT')
    <input type="hidden" name="agreements[status]" value="commit">
  @endif
    @component('students.forms.agreement', ['item' => $item->student, 'domain' => $domain, 'input' => $input,  'agreement' => $item, 'user'=>$user]) @endcomponent
  @if(isset($_edit) && $_edit == true)
    <div class="row">
      <div class="col-12 col-md-6 mb-1">
        <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="agreement_correction" confirm="契約を修正を確定しますか？">
          <i class="fa fa-envelope mr-1"></i>
          更新
        </button>
      </div>
      <div class="col-12 col-md-6 mb-1">
        <a href="javascript:void(0);" data-dismiss="modal" role="button" class="btn btn-secondary btn-block float-left mr-1">
          <i class="fa fa-arrow-circle-left mr-1"></i>
          {{__('labels.cancel_button')}}
        </a>
      </div>
    </div>
  </from>
  @endif
</div>
