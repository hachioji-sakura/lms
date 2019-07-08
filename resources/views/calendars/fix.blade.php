@component('calendars.page', ['item' => $item, 'fields' => $fields, 'domain' => $domain, 'action' => $action, 'user'=>$user])
  @slot('page_message')
  @if($user->role=="parent" || $user->role=="student")
    {{__('messages.confirm_calendar_fix')}}
  @endif
  @endslot
  @slot('forms')
  <form method="POST" action="/calendars/{{$item['id']}}" id="_form">
    @csrf
    @method('PUT')
    @if($user->role=="parent" || $user->role=="student")
      <div class="row">
        @component('calendars.forms.fix_form', ['item' => $item, 'user'=>$user]) @endcomponent
        @component('calendars.forms.target_member', ['item' => $item, 'user'=>$user, 'status'=>'fix', 'student_id' => $student_id]) @endcomponent
      </div>
      <div class="row">
      <div class="col-12 mb-1">
          <button type="button" class="btn btn-submit btn-info btn-block"  accesskey="_form" confirm="{{__('messages.confirm_calendar_fix')}}">
            <i class="fa fa-envelope mr-1"></i>
            {{__('labels.send_button')}}
          </button>
        </form>
      </div>
    @endif
    <div class="col-12 col-lg-12 col-md-12 mb-1">
        <button type="reset" class="btn btn-secondary btn-block">
          {{__('labels.close_button')}}
        </button>
    </div>
    <script>
    $(function(){
      base.pageSettinged("_form", null);
      //submit
      $("button.btn-submit").on('click', function(e){
        e.preventDefault();
        if(front.validateFormValue('_form')){
          $("form").submit();
        }
      });
    });

    </script>
  </form>
  @endslot
@endcomponent
