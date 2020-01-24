@include('calendar_settings.forms.to_calendar_form')
<div class="direct-chat-msg">
  <form id="edit" method="POST" action="/{{$domain}}/to_calendar">
  <input type="hidden" name="user_id" value="{{$target_user_id}}" / >
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    <div id="calendar_settings_to_calendar">
      @yield('input_form')
      @yield('setting_form')
      <div class="row">
        <div class="col-12 mb-1">
            <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="to_calendar_create" confirm="{{__('messages.confirm_add')}}">
              {{__('labels.add_button')}}
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
  base.pageSettinged("calendar_settings_to_calendar", form_data);
  //submit
  $("button.btn-submit").on('click', function(e){
    e.preventDefault();
    if(front.validateFormValue('calendar_settings_to_calendar')){
      $('form').submit();
    }
  });
});
</script>
