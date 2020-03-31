@component('calendars.page', ['item' => $item, 'fields' => $fields, 'domain' => $domain, 'action'=>'', 'user'=>$user])
  @slot('page_message')
    {!!nl2br(__('messages.confirm_calendar_cancel'))!!}
  @endslot
  @slot('forms')
  <div id="{{$domain}}_cancel">
  <form method="POST" action="/calendars/{{$item['id']}}/cancel">
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    <input type="hidden" name="is_all_student" value="1" />
    @method('PUT')
    <div class="row">
      <div class="col-12">
        <div class="form-group">
          <label for="cancel_reason" class="w-100">
            {{__('labels.cancel_reason')}}
            <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
          </label>
          <textarea type="text" name="cancel_reason" class="form-control" placeholder="" ></textarea>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-6 mb-1" id="{{$domain}}_confirm">
        <button type="button" class="btn btn-submit btn-success btn-block"  accesskey="{{$domain}}_cancel" confirm="この予定をキャンセルしますか？">
            {{__('labels.update_button')}}
        </button>
      </div>
      <div class="col-6 mb-1">
        <button type="reset" class="btn btn-secondary btn-block">
            {{__('labels.close_button')}}
        </button>
      </div>
    </div>
  </form>
  </div>
  @endslot
@endcomponent
