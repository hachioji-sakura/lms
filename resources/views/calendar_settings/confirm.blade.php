@component('calendar_settings.page', ['item' => $item, 'fields' => $fields, 'domain' => $domain, 'action' => '', 'user'=>$user])
  @slot('page_message')
  {{--TODO 講師に代わって予定確認できるようにするが、暫定とし、運用が回ったら解除したい --}}
    @if(isset($user) && $user->role==="manager")
    <div class="col-12 bg-danger p-2 mb-2">
      <i class="fa fa-exclamation-triangle mr-1"></i>{!!nl2br(__('messages.confirm_calendar_confirm_for_teacher'))!!}
    </div>
    @else
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
      <div class="col-12 p-0 mb-1" id="{{$domain}}_confirm">
        <form method="POST" action="/calendar_settings/{{$item['id']}}">
          @csrf
          <input type="text" name="dummy" style="display:none;" / >
          @method('PUT')
        @component('calendars.forms.to_status_form', ['item'=>$item, 'attributes' => $attributes]) @endcomponent
          <div class="col-12 mb-1" >
            <input type="hidden" name="is_all_student" value="1" />
            <button type="button" class="btn btn-submit btn-success btn-block"  accesskey="{{$domain}}_confirm">
                <i class="fa fa-check mr-1"></i>
                {{__('labels.schedule_to_confirm')}}
            </button>
          </div>
      </form>
    </div>
    @elseif(isset($user) && $user->role==="manager" && $item->user_id != $user->user_id)
    <div class="col-12 mb-1" id="{{$domain}}_confirm">
      <form method="POST" action="/calendar_settings/{{$item['id']}}/remind">
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
      <form method="POST" action="/calendar_settings/{{$item['id']}}/status_update/fix">
        @csrf
    		<input type="text" name="dummy" style="display:none;" / >
        @method('PUT')
        <button type="button" class="btn btn-submit btn-success btn-block"  accesskey="{{$domain}}_confirm" confirm="この予定を確定しますか？">
            <i class="fa fa-envelope mr-1"></i>
              {{__('labels.update_button')}}
        </button>
      </form>
    </div>
    @endif

    <div class="col-12 col-lg-6 mb-1" id="{{$domain}}_action">
      <form method="POST" action="/calendar_settings/{{$item['id']}}">
        @csrf
        <input type="text" name="dummy" style="display:none;" / >
        @method('DELETE')
        <button type="button" class="btn btn-submit btn-danger btn-block"  accesskey="{{$domain}}_action" confirm="{{__('messages.confirm_delete')}}">
          <i class="fa fa-trash-alt mr-1"></i>
          {{__('labels.schedule_delete')}}
        </button>
      </form>
    </div>
    <div class="col-12 col-lg-6 mb-1">
      <button type="reset" class="btn btn-secondary btn-block">
          {{__('labels.close_button')}}
      </button>
    </div>
  </div>
  @endslot
@endcomponent
