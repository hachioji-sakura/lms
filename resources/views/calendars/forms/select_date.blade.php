<div class="col-12">
  <div class="form-group">
    <label for="start_date" class="w-100">
      {{__('labels.date')}}
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
      </div>
      <input type="text" id="start_date" name="start_date" class="form-control float-left" required="true" uitype="datepicker" placeholder="例：{{date('Y/m/d')}}"
      @if(isset($_edit) && $_edit==true && isset($item) && isset($item['start_time']))
        value="{{date('Y/m/d', strtotime($item['start_time']))}}"
      @elseif(isset($item) && isset($item['start_date']))
        value="{{$item['start_date']}}"
      @endif
      @if(!(isset($_edit) && $_edit==true))
      @endif
      >
      <div class="form-check mt-2 text-danger">
          <input type="hidden" name="is_temporary"
          @if($item->is_temporary()==true)
          value="true"
          @else
          value="false"
          @endif
          />
          <input class="form-check-input icheck flat-red" type="checkbox" name="_is_temporary" id="is_temporary" value="true"
          @if($_edit==false || ($_edit==true && $item->is_temporary()==true))
            checked
          @endif
          onChange="is_temporary_click()"
          >
          <i class="fa fa-lock"></i>
          <label class="form-check-label" for="is_temporary">
              {{__('labels.temporary_schedule')}}
          </label>
          <script>
          $(function(){
            is_temporary_click();
          });
          function is_temporary_click(){
            $('input[name="is_temporary"]').val('false');
            $('.schedule_change_remind').show();
            $('.is_temporary_message').hide();
            if($('input[name="_is_temporary"]').prop('checked')==true){
              $('input[name="is_temporary"]').val('true');
              $('.schedule_change_remind').hide();
              $('.is_temporary_message').show();
            }
          }
          </script>
      </div>

    </div>
  </div>
</div>
