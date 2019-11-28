@component('calendars.page', ['item' => $item, 'fields' => $fields, 'action'=>$action, 'domain' => $domain, 'user'=>$user])
  @slot('page_message')
    {!!nl2br(__('messages.confirm_lecture_cancel'))!!}
    <div class="col-12 col-lg-12 col-md-12 mb-1">
      <span class="text-danger">
        {!!nl2br(__('messages.warning_lecture_cancel'))!!}
      </span>
    </div>
  @endslot
  @slot('forms')
  <div id="{{$domain}}_action">
    <form method="POST" action="/calendars/{{$item['id']}}/status_update/lecture_cancel">
      @csrf
      <input type="text" name="dummy" style="display:none;" / >
      @method('PUT')
      @if(isset($student_id))
        <input type="hidden" value="{{$student_id}}" name="student_id" />
      @endif
      <div class="row">
        <div class="col-12 col-md-6 mb-1">
            <button type="button" class="btn btn-submit btn-danger btn-block"  accesskey="{{$domain}}_action" confirm="{{__('messages.confirm_lecture_cancel')}}">
              <i class="fa fa-envelope mr-1"></i>
              {{__('labels.send_button')}}
            </button>
        </div>
        <div class="col-12 col-md-6 mb-1">
            <button type="reset" class="btn btn-secondary btn-block">
              {{__('labels.close_button')}}
            </button>
        </div>
      </div>
    </form>
  </div>
  @endslot
@endcomponent
