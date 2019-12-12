@component('calendars.page', ['item' => $item, 'fields' => $fields, 'domain' => $domain, 'action'=>'', 'user'=>$user])
  @slot('page_message')
  @if($item['status']==='rest')
  講師あてに休み連絡を再送します。
  @elseif($item['status']==='confirm')
  生徒あてに授業予定確認をを再送します。
  @elseif($item['status']==='fix')
  生徒、講師あてに授業予定を再送します。
  @endif
  @endslot
  @slot('forms')
  <div id="{{$domain}}_action">
    <form method="POST" action="/calendars/{{$item['id']}}/remind" >
      @csrf
      <input type="text" name="dummy" style="display:none;" / >
      @method('PUT')
      <input type="hidden" value="1" name="is_all_student" />
      @if(isset($student_id))
        <input type="hidden" value="{{$student_id}}" name="student_id" />
      @endif
    <div class="row">
      <div class="col-12 col-md-6 mb-1">
          <button type="button" class="btn btn-submit btn-danger btn-block"  accesskey="{{$domain}}_action" confirm="{{__('messages.confirm_calendar_remind')}}">
            <i class="fa fa-envelope mr-1"></i>
              {{__('labels.remind_button')}}
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
