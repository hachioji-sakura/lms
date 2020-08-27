@component('components.page', ['item' => $item, 'fields' => $fields, 'domain' => $domain, 'action' => $action])
  @slot('page_message')
  {!!nl2br(__('messages.confirm_ask_remind'))!!}
  @endslot
  @slot('forms')
    <div class="row">
    <div class="col-12 mb-1" id="commit_form">
      <form method="POST" action="/asks/{{$item['id']}}/status_update/remind">
        @csrf
        <input type="text" name="dummy" style="display:none;" / >
        @method('PUT')
        <button type="button" class="btn btn-submit btn-danger btn-block"  accesskey="{{$domain}}_action" confirm="{{__('messages.confirm_calendar_remind')}}">
          <i class="fa fa-envelope mr-1"></i>
            {{__('labels.remind_button')}}
        </button>
      </form>
    </div>
    <div class="col-12 mb-1">
        <button type="reset" class="btn btn-secondary btn-block">
            {{__('labels.close_button')}}
        </button>
    </div>
    <script>
    $(function(){
      base.pageSettinged("commit_form", null);
      //submit
      $("#commit_form button.btn-submit").on('click', function(e){
        e.preventDefault();
        if(front.validateFormValue('commit_form')){
          $(this).prop("disabled",true);
          $("#commit_form form").submit();
        }
      });
    });

    </script>
  @endslot
@endcomponent
