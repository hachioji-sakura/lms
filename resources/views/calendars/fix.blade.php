@component('calendars.page', ['item' => $item, 'fields' => $fields, 'domain' => $domain, 'action' => $action, 'user'=>$user])
  @slot('page_message')
  @if($item->is_passed()==true)
    <div class="col-12 bg-danger p-2 mb-2">
      <i class="fa fa-exclamation-triangle mr-1"></i>この予定は過ぎているため、操作はできません。
    </div>
  @else
    @if($user->role=="parent" || $user->role=="student")
    <div class="col-12 bg-danger p-2 mb-2">
      <i class="fa fa-exclamation-triangle mr-1"></i>{!!nl2br(__('messages.confirm_calendar_fix'))!!}
    </div>
    @endif
    @if($user->role==="manager" || $user->role==="teacher")
    <div class="col-12 bg-danger p-2 mb-2">
      <i class="fa fa-exclamation-triangle mr-1"></i>{!!nl2br(__('messages.info_calendar_to_fix'))!!}
    </div>
    @endif
  @endif
  @endslot
  @slot('forms')
  <form method="POST" action="/calendars/{{$item['id']}}" id="_form">
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
        </form>
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
