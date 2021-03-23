<div id="agreement_correction">
  @if(isset($_edit) && $_edit == true)
  <form method="POST"  action="/agreements/{{$item->id}}">
    @csrf
    @method('PUT')
    <div class="form-group">
      <label for="status" class="w-100">
        {{__('labels.status')}}
        <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
      </label>
      <div class="input-group">
        <div class="form-check">
            <input class="form-check-input icheck flat-green" type="radio" name="agreements[status]" id="status_commit" value="commit" required="true" checked>
            <label class="form-check-label" for="status_commit">
                {{config('attribute.agreement_status')['commit']}}
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input icheck flat-green" type="radio" name="agreements[status]" id="status_commit" value="dummy" required="true">
            <label class="form-check-label" for="status_commit">
                {{config('attribute.agreement_status')['dummy']}}
            </label>
        </div>
      </div>
    </div>
  @endif
    @component('students.forms.agreement', ['item' => $item->student, 'domain' => $domain, 'input' => $is_money_edit,  'agreement' => $item, 'user'=>$user]) @endcomponent
  @if(isset($_edit) && $_edit == true)
    <div class="row">
      <div class="col-12 col-md-6 mb-1">
        <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="agreement_correction" confirm="{{__('messages.confirm_update')}}">
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
