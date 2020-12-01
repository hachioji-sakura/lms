@component('calendars_settings.page', ['item' => $item, 'fields' => $fields, 'domain' => $domain, 'action' => $action, 'user'=>$user])
  @slot('page_message')
  @if($item->is_passed()==true)
    <div class="col-12 bg-danger p-2 mb-2">
      <i class="fa fa-exclamation-triangle mr-1"></i>{!!nl2br(__('messages.error_passed_calendar'))!!}
    </div>
  @else
    @if($user->role=="parent" || $user->role=="student")
      <div class="col-12 bg-success p-2 mb-2">
        <i class="fa fa-exclamation-triangle mr-1"></i>{!!nl2br(__('messages.confirm_calendar_fix'))!!}
      </div>
    @elseif($user->role==="manager" || $user->role==="teacher")
    <div class="col-12 bg-danger p-2 mb-2">
      <i class="fa fa-exclamation-triangle mr-1"></i>生徒の授業予定を確定します。
    </div>
    @endif
  @endif
  @endslot
  @slot('forms')
  <form method="POST" action="/calendar_settings/{{$item['id']}}" id="_form">
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    @method('PUT')
    @if($user->role==="manager" || $user->role==="teacher")
      <input type="hidden" value="1" name="is_proxy">
    @endif
    <div class="row">
    @if($item->is_passed()==true)
      <div class="col-12 mb-1">
          <button type="reset" class="btn btn-secondary btn-block">
            {{__('labels.close_button')}}
          </button>
      </div>
    @else
      @component('calendars.forms.fix_form', ['item' => $item, 'user'=>$user]) @endcomponent
      @component('calendars.forms.target_member', ['item' => $item, 'user'=>$user, 'status'=>'fix', 'student_id' => $student_id]) @endcomponent
      <div class="col-12 mb-1">
          <button type="button" class="btn btn-submit btn-info btn-block"  accesskey="_form" confirm="{{__('messages.confirm_calendar_fix')}}">
            <i class="fa fa-envelope mr-1"></i>
            {{__('labels.send_button')}}
          </button>
      </div>
      <div class="col-12 mb-1">
          <button type="reset" class="btn btn-secondary btn-block">
            {{__('labels.close_button')}}
          </button>
      </div>
    @endif
    </div>
    <script>
    $(function(){
      base.pageSettinged("_form", null);
      //submit
      $("button.btn-submit").on('click', function(e){
        e.preventDefault();
        if(front.validateFormValue('_form')){
          $(this).prop("disabled",true);
          $("form").submit();
        }
      });
    });

    </script>
  </form>
  @endslot
@endcomponent
