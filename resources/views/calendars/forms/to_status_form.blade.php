@if($item->is_teaching()==true)
  <div class="col-12 mt-2">
    <div class="form-group">
      <label for="status_confirm" class="w-100">
        {{__('labels.schedule_confirm')}}
        <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
      </label>
      <div class="input-group" >
        <input class="form-check-input icheck flat-green" type="radio" name="dummy_status" id="status_confirm" value="confirm" required="true" onChange="select_action_change();"
               checked
        >
        <label class="form-check-label mr-3" for="status_confirm">
          {{__('labels.schedule_to_confirm')}}
        </label>
        <input class="form-check-input icheck flat-green" type="radio" name="dummy_status" id="status_cancel" value="cancel" required="true" onChange="select_action_change();"
        >
        <label class="form-check-label mr-3" for="status_cancel">
          {{__('labels.do_cancel')}}
        </label>
      </div>
    </div>
  </div>
  <div class="col-12 mt-2 collapse student_confirm_form">
    <div class="form-group">
      <label for="send_mail" class="w-100">
        {{__('labels.updated_status')}}
        <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
      </label>
      <div class="input-group" >
        <input class="form-check-input icheck flat-green" type="radio" name="dummy_status_student" id="status_fix_student" value="fix" required="true" onChange="student_confirm_change()">
        <label class="form-check-label mr-3" for="status_fix_student">
          {{__('labels.not_require_student_confirm')}}
        </label>
        <input class="form-check-input icheck flat-green" type="radio" name="dummy_status_student" id="status_confirm_student" value="confirm" required="true" onChange="student_confirm_change()">
        <label class="form-check-label mr-3" for="status_confirm_student">
          {{__('labels.require_student_confirm')}}
        </label>
      </div>
    </div>
  </div>

  <div class="status_change_form" style="display:none;">
    <div class="col-12 mb-2">
      <label for="cancel_reason" class="w-100">
        {{__('labels.cancel_reason')}}
        <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
      </label>
      <textarea id="cancel_reason" name="cancel_reason" class="form-control" placeholder=""></textarea>
    </div>
  </div>
  <input type="hidden" name="status" value="" id="hidden_status">
@else
<div class="col-12 mt-2">
  <div class="form-group">
    <label for="to_status" class="w-100">
      {{__('labels.updated_status')}}
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    <div class="input-group" >
      <input class="form-check-input icheck flat-grey" type="radio" name="status" id="to_status_fix" value="fix" required="true" checked>
      <label class="form-check-label mr-3" for="to_status_fix" checked>
        {{__('labels.not_require_student_confirm')}}
      </label>
      <input class="form-check-input icheck flat-red" type="radio" name="status" id="to_status_confirm" value="confirm" required="true" >
      <label class="form-check-label mr-3" for="to_status_confirm">
        {{__('labels.require_student_confirm')}}
      </label>
    </div>
  </div>
</div>
@endif
<script>
      //初期表示
      $(function(){
          $('.status_change_form').hide();
          $('.student_confirm_form').show();
          select_action_change();
      });

      function select_action_change(obj){
        var confirm = $('#status_confirm').is(':checked');
        var cancel = $('#status_cancel').is(':checked');

        if(confirm){
          $('.status_change_form').hide();
          $('.student_confirm_form').show();

          if($('#status_fix_student').is(':checked')){
            $('#hidden_status').val('fix');
          }else if ($('#status_confirm_student').is(':checked')){
            $('#hidden_status').val('confirm');
          }else{
            $('#hidden_status').val('confirm');
          }
        }
        else if(cancel){
          $('.status_change_form').show();
          $('.student_confirm_form').hide();
          $('#hidden_status').val('cancel');
        }
      }
      function student_confirm_change(obj){
        var status_fix_student = $('#status_fix_student').is(':checked');
        var status_confirm_student = $('#status_confirm_student').is(':checked');
        if(status_confirm_student){
          $('#hidden_status').val('confirm');
        }
        else if(status_fix_student){
          $('#hidden_status').val('fix');
        }
      }
  </script>
