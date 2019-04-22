<div class="col-12">
  <div class="card card-widget mb-2">
    <div class="card-header">
      <i class="fa fa-clock mr-1"></i>体験授業日時
    </div>
    <div class="card-footer">
      <div class="row">
        <div class="col-6 border-right">
          <div class="description-block">
            <h5 class="description-header">
              <i class="fa fa-calendar-check mr-1"></i>
              希望日時１
            </h5>
            <span class="description-text">
              @if(count($candidate_teacher->trial1) < 1)
                希望日時１は空いていません
              @else
                @foreach($candidate_teacher->trial1 as $i=>$_list)
                  @if($_list['free'])
                  <div class="form-check ml-2" id="trial1_select">
                    <input class="form-check-input icheck flat-green" type="radio" name="teacher_schedule" id="trial1_{{$i}}"
                     value="{{$_list['start_time']}}_{{$_list['end_time']}}"
                     dulation="{{$_list['dulation']}}"
                     start_time="{{$_list['start_time']}}"
                     end_time="{{$_list['end_time']}}"
                     onChange="teacher_schedule_change(this)"
                     validate="teacher_schedule_validate('#trial1_select')">
                    <label class="form-check-label" for="trial1_{{$i}}">
                      {{$_list['dulation']}}
                    </label>
                  </div>
                  @else
                  {{-- 空いてない --}}
                  <div class="form-check ml-2">
                    <label class="form-check-label" for="trial1_{{$i}}">
                      <i class="fa fa-calendar-times mr-1"></i>
                      {{$_list['dulation']}}
                    </label>
                  </div>
                  @endif
                @endforeach
              @endif
            </span>
          </div>
        </div>
        <div class="col-6">
          <div class="description-block">
            <h5 class="description-header">
              <i class="fa fa-calendar-check mr-1"></i>
              希望日時2
            </h5>
            <span class="description-text">
              @if(count($candidate_teacher->trial2) < 1)
                希望日時２は空いていません
              @else
                @foreach($candidate_teacher->trial2 as $i=>$_list)
                  @if($_list['free'])
                  <div class="form-check ml-2" id="trial2_select">
                    <input class="form-check-input icheck flat-green" type="radio" name="teacher_schedule" id="trial2_{{$i}}"
                     value="{{$_list['start_time']}}_{{$_list['end_time']}}"
                     dulation="{{$_list['dulation']}}"
                     start_time="{{$_list['start_time']}}"
                     end_time="{{$_list['end_time']}}"
                     onChange="teacher_schedule_change(this)"
                     validate="teacher_schedule_validate('#trial2_select')">
                    <label class="form-check-label" for="trial2_{{$i}}">
                      {{$_list['dulation']}}
                    </label>
                  </div>
                  @else
                  {{-- 空いてない --}}
                  <div class="form-check ml-2">
                    <label class="form-check-label" for="trial2_{{$i}}">
                      <i class="fa fa-calendar-times mr-1"></i>
                      {{$_list['dulation']}}
                    </label>
                  </div>
                  @endif
                @endforeach
              @endif
            </span>
          </div>
        </div>
        <script >
        function teacher_schedule_change(obj){
          var _teacher_schedule = $('input[name=teacher_schedule]:checked');
          $('input[name=start_time]').val(_teacher_schedule.attr('start_time'));
          $('input[name=end_time]').val(_teacher_schedule.attr('end_time'));
        }
        function teacher_schedule_validate(obj){
          var start_time = $('input[name=start_time]').val();
          var end_time = $('input[name=end_time]').val();
          console.log("teacher_schedule_validate"+start_time+":"+end_time);
          var is_school = $('input[type="checkbox"][name="lesson[]"][value="1"]').prop("checked");
          if(!util.isEmpty(start_time) && !util.isEmpty(end_time)) return true;
          front.showValidateError(obj, '体験授業日時を指定してください');
          return false;
        }
        </script>
      </div>
    </div>
  </div>
</div>
