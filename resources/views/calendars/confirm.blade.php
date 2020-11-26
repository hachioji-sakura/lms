@component('calendars.page', ['item' => $item, 'fields' => $fields, 'domain' => $domain, 'action'=>'', 'user'=>$user])
  @slot('page_message')
    @if($item->is_passed()==true)
    <div class="col-12 bg-danger p-2 mb-2">
      <i class="fa fa-exclamation-triangle mr-1"></i>{!!nl2br(__('messages.error_passed_calendar'))!!}
    </div>
    @else
    {{--TODO 講師に代わって予定確認できるようにするが、暫定とし、運用が回ったら解除したい --}}
    <div class="col-12 bg-danger p-2 mb-2">
      <i class="fa fa-exclamation-triangle mr-1"></i>{!!nl2br(__('messages.info_calendar_confirm'))!!}
    </div>
    @endif
  @endslot
  @slot('forms')
  @method('PUT')
  <div class="row">
    {{--TODO 講師に代わって予定確認できるようにするが、暫定とし、運用が回ったら解除したい --}}
    @if(isset($user) && ($user->role==="teacher" || $user->role==="manager"))
      {{-- 講師の場合 --}}
      @if($item->is_passed()==true)
      @else
      <div class="col-12 p-0 mb-1" id ="{{$domain}}_confirm">
        <form method="POST" action="/calendars/{{$item['id']}}">
        @csrf
        <input type="text" name="dummy" style="display:none;" / >
        @method('PUT')
        @component('calendars.forms.to_status_form', ['item'=>$item, 'attributes' => $attributes]) @endcomponent
          <div class="col-12 mb-1" id="{{$domain}}_confirm">
            <input type="hidden" name="is_all_student" value="1" />
            <button type="button" class="btn btn-submit btn-success btn-block"  accesskey="{{$domain}}_confirm">
                <i class="fa fa-check mr-1"></i>
                {{__('labels.schedule_to_confirm')}}
            </button>
          </div>
        </form>
      </div>
      @endif
    @elseif(isset($user) && $user->role==="manager" && $item->user_id != $user->user_id)
    <div class="col-12 mb-1" id="{{$domain}}_confirm">
      <form method="POST" action="/calendars/{{$item['id']}}/remind">
        @csrf
    		<input type="text" name="dummy" style="display:none;" / >
        @method('PUT')
        <button type="button" class="btn btn-submit btn-success btn-block"  accesskey="{{$domain}}_confirm" confirm="この予定を講師に連絡しますか？">
            <i class="fa fa-envelope mr-1"></i>
              {{__('labels.send_button')}}
        </button>
      </form>
    </div>
    @elseif(isset($user) && ($user->role==="manager" || $user->role==="staff") && $item->user_id == $user->user_id)
    <div class="col-12 mb-1" id="{{$domain}}_confirm">
      <form method="POST" action="/calendars/{{$item['id']}}/status_update/fix">
        @csrf
    		<input type="text" name="dummy" style="display:none;" / >
        @method('PUT')
        <button type="button" class="btn btn-submit btn-success btn-block"  accesskey="{{$domain}}_confirm" confirm="{{__('labels.confirm_calendar_confirm')}}">
            <i class="fa fa-envelope mr-1"></i>
              {{__('labels.update_button')}}
        </button>
      </form>
    </div>
    @endif

    @if($item['trial_id'] < 1)
    @endif
    <div class="col-12 mb-1">
      <button type="reset" class="btn btn-secondary btn-block">
          {{__('labels.close_button')}}
      </button>
    </div>
  </div>
  @endslot
@endcomponent
