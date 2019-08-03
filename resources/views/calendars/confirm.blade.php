@component('calendars.page', ['item' => $item, 'fields' => $fields, 'domain' => $domain, 'action'=>'', 'user'=>$user])
  @slot('page_message')
    @if(isset($user) && $user->role==="teacher")
    @elseif(isset($user) && $user->role==="manager")
    {!!nl2br(__('messages.confirm_calendar_confirm_for_teacher'))!!}
    @else
    {!!nl2br(__('messages.info_calendar_confirm'))!!}
    @endif
  @endslot
  @slot('forms')
  @method('PUT')
  <div class="row">
@if(isset($user) && $user->role==="manager")
<div class="col-12 col-lg-6 col-md-6 mb-1" id="{{$domain}}_action">
  <form method="POST" action="/calendars/{{$item['id']}}">
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    @method('DELETE')
    <button type="button" class="btn btn-submit btn-danger btn-block"  accesskey="{{$domain}}_action" confirm="この予定を削除しますか？">
      <i class="fa fa-trash-alt mr-1"></i>
      {{__('labels.schedule_delete')}}
    </button>
  </form>
</div>
<div class="col-12 col-lg-6 col-md-6 mb-1" id="{{$domain}}_confirm">
  <form method="POST" action="/calendars/{{$item['id']}}/status_update/remind">
    @csrf
		<input type="text" name="dummy" style="display:none;" / >
    @method('PUT')
    <button type="button" class="btn btn-submit btn-success btn-block"  accesskey="{{$domain}}_confirm" confirm="この予定を講師に連絡しますか？">
        <i class="fa fa-envelope mr-1"></i>
          {{__('labels.send_button')}}
    </button>
  </form>
</div>
@elseif(isset($user) && $user->role==="teacher")
    @if($item['trial_id'] < 1 && $item['status']==='new')
    <div class="col-12 col-lg-6 col-md-6 mb-1" id="{{$domain}}_confirm">
      <form method="POST" action="/calendars/{{$item['id']}}/status_update/confirm">
        @csrf
		    <input type="text" name="dummy" style="display:none;" / >
        @method('PUT')
        <input type="hidden" value="1" name="is_all_student" />
        <button type="button" class="btn btn-submit btn-success btn-block"  accesskey="{{$domain}}_confirm" confirm="この予定を生徒に連絡しますか？">
            <i class="fa fa-envelope mr-1"></i>
            {{__('labels.schedule_fix')}}
        </button>
      </form>
    </div>
    <div class="col-12 col-lg-6 col-md-6 mb-1" id="{{$domain}}_action">
      <form method="POST" action="/calendars/{{$item['id']}}">
        @csrf
		    <input type="text" name="dummy" style="display:none;" / >
        @method('DELETE')
        <button type="button" class="btn btn-submit btn-danger btn-block"  accesskey="{{$domain}}_action" confirm="この予定を削除しますか？">
          <i class="fa fa-trash-alt mr-1"></i>
          {{__('labels.schedule_delete')}}
        </button>
      </form>
    </div>
    @else
    <div class="col-12 col-lg-12 col-md-12 mb-1" id="{{$domain}}_confirm">
      <form method="POST" action="/calendars/{{$item['id']}}/status_update/confirm">
        @csrf
		    <input type="text" name="dummy" style="display:none;" / >
        @method('PUT')
        <button type="button" class="btn btn-submit btn-success btn-block"  accesskey="{{$domain}}_confirm" confirm="この予定を生徒に連絡しますか？">
            <i class="fa fa-envelope mr-1"></i>
            {{__('labels.schedule_remind')}}
        </button>
      </form>
    </div>
    @endif
@endif
    <div class="col-12 col-lg-12 col-md-12 mb-1">
        <button type="reset" class="btn btn-secondary btn-block">
            {{__('labels.close_button')}}
        </button>
    </div>
  </div>
  @endslot
@endcomponent
