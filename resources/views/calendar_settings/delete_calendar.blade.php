@include('calendar_settings.forms.delete_calendar_form')
<div class="direct-chat-msg">
  <form id="edit" method="POST" action="/{{$domain}}/{{$item->id}}/delete_calendar">
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    <div id="calendar_settings_delete_calendar">
      @yield('input_form')
      @yield('setting_form')
      <div class="row">
        <div class="col-12 mb-1">
            <button type="button" class="btn btn-submit btn-danger btn-block collapse" accesskey="delete_calendar_create" confirm="{{__('messages.confirm_delete')}}">
              {{__('labels.schedule_delete')}}
              <i class="fa fa-caret-right ml-1"></i>
            </button>
        </div>
        <div class="col-12 mb-1">
          <button type="reset" class="btn btn-secondary btn-block">
              {{__('labels.close_button')}}
          </button>
        </div>
      </div>
    </div>
  </form>
</div>
<script>

$(function(){
  var form_data = null;
  base.pageSettinged("calendar_settings_delete_calendar", form_data);
  //submit
  $("button.btn-submit").on('click', function(e){
    e.preventDefault();
    if(front.validateFormValue('calendar_settings_delete_calendar') && select_ids_check_validate()){
      $(this).prop("disabled",true);
      $('form').submit();
    }
  });
});
</script>
