@component('components.page', ['item' => $item, 'fields' => $fields, 'domain' => $domain, 'action' => $action])
  @slot('page_message')
  {!!nl2br(__('messages.confirm_ask_cancel'))!!}
  @endslot
  @slot('forms')
    <div class="row">
    <div class="col-12 mb-1" id="cancel_form">
      <form method="POST" action="/asks/{{$item['id']}}/status_update/cancel">
        @csrf
        <input type="text" name="dummy" style="display:none;" / >
        @method('PUT')
        <button type="button" class="btn btn-submit btn-danger btn-block"  accesskey="cancel_form" confirm="{{__('messages.confirm_update')}}">
          <i class="fa fa-times mr-1"></i>
          差戻
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
      base.pageSettinged("cancel_form", null);
      //submit
      $("#cancel_form button.btn-submit").on('click', function(e){
        e.preventDefault();
        if(front.validateFormValue('cancel_form')){
          $(this).prop("disabled",true);
          $("#cancel_form form").submit();
        }
      });
    });

    </script>
  @endslot
@endcomponent
