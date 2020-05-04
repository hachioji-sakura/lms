@component('calendars.page', ['item' => $item, 'fields' => $fields, 'action'=>$action, 'domain' => $domain, 'user'=>$user])
  @slot('page_message')
    <div class="col-12 bg-warning p-2 mb-2">
      <i class="fa fa-exclamation-triangle mr-1"></i>
      ステータスを変更します。
    </div>
  @endslot
  @slot('forms')
  <div id="{{$domain}}_member_setting">
    <form id="edit" method="POST" action="/{{$domain}}/{{$item['id']}}/members/setting">
      @method('PUT')
      @csrf
      <input type="text" name="dummy" style="display:none;" / >
      <div class="row">
      @if(count($item["students"])>1)
        <div class="col-6">
          <div class="form-group">
            <label for="title" class="w-100">
              {{__('labels.students')}}
              <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
            </label>
            <select name="student_id[]" class="form-control select2" multiple="multiple" width=100% placeholder="{{__('labels.charge_student')}}" required="true" >
              @foreach($item["students"] as $member)
                 <option
                 value="{{ $member->user->student->id }}"
                 member_id ="{{ $member->id }}"
                 rest_type ="{{ $member->rest_type }}"
                 rest_result ="{{ $member->rest_result }}"
                 >{{$member->user->student->name()}}</option>
              @endforeach
            </select>
          </div>
        </div>
      @else
      @foreach($item["students"] as $member)
        <input type="hidden" name='student_id[]'
         value="{{ $member->user->student->id }}"
         member_id ="{{ $member->id }}"
         rest_type ="{{ $member->rest_type }}"
         rest_result ="{{ $member->rest_result }}"
         ></input>
      @endforeach
      @endif
      <div class="col-6 mt-2">
        <div class="form-group">
          <label for="send_mail" class="w-100">
            更新内容
            <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          </label>
          <div class="input-group" >
            <input class="form-check-input icheck flat-green" type="radio" name="action" id="action_status_update" value="status_update" required="true" onChange="select_action_change();"
            checked
            >
            <label class="form-check-label mr-3" for="action_status_update">
              ステータス更新
            </label>
            <input class="form-check-input icheck flat-green" type="radio" name="action" id="action_remind" value="remind" required="true" onChange="select_action_change();"
            >
            <label class="form-check-label mr-3" for="action_remind">
              予定に関する通知連絡
            </label>
          </div>
        </div>
      </div>
      <div class="col-6 mt-2 status_change_form">
        <div class="form-group">
          <label for='to_status' class="w-100">
            {{__('labels.status')}}
            <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          </label>
          <div class="input-group">
            <select name='to_status' class="form-control" required="true" onChange="select_status_change()">
              @foreach(config('attribute.calendar_status') as $index=>$name)
                <option value="{{$index}}" >
                    {{$name}}({{$index}})
                </option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
    </div>
    <div class="row rest_type_change" style="display:none;">
      <div class="col-6">
          <label for="course_minutes" class="w-100">
            休み種別
            <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          </label>
          <label class="mx-2" for="rest_type_1">
            <input type="radio" value="a1" name="rest_type" class="icheck flat-green">
            休み１
          </label>
          <label class="mx-2" for="rest_type_2">
            <input type="radio" value="a2" name="rest_type" class="icheck flat-green">
            休み２
          </label>
      </div>
      <div class="col-6 mb-2">
          <label for="course_minutes" class="w-100">
            休み理由
            <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
          </label>
          {{-- TODO 休み理由は選択型の方がよさそうだが、要件がきまっていないので、いったんtextにする --}}
          <input type="text" name="rest_result" class="form-control" placeholder="" inputtype="zenkaku">
      </div>
    </div>
      <script>
      $(function(){
        base.pageSettinged("{{$domain}}_member_setting", []);
      });
      function select_status_change(){
        $('.rest_type_change').hide();
        var status = $('select[name="to_status"]').val();
        if(status=='rest'){
          $('.rest_type_change').show();
        }
      }
      function select_action_change(){
        $('.status_change_form').hide();
        var action = $('input[name="action"]:checked').val();
        if(action !='remind'){
          $('.status_change_form').show();
        }
      }
      </script>
      <div class="row">
        <div class="col-12 col-md-6 mb-1">
            <button type="button" class="btn btn-submit btn-danger btn-block"  accesskey="{{$domain}}_member_setting">
              {{__('labels.update_button')}}
            </button>
        </div>
        <div class="col-12 col-md-6 mb-1">
            <button type="reset" class="btn btn-secondary btn-block">
              {{__('labels.close_button')}}
            </button>
        </div>
      </div>
    </form>
  </div>
  @endslot
@endcomponent
