@component('calendars.page', ['item' => $item, 'fields' => $fields, 'action'=>$action, 'domain' => $domain, 'user'=>$user])
  @slot('page_message')
    <div class="col-12 bg-warning p-2 mb-2">
      <i class="fa fa-exclamation-triangle mr-1"></i>
      休み判定結果を変更します。
    </div>
  @endslot
  @slot('forms')
  <div id="{{$domain}}_action">
    <form id="edit" method="POST" action="/{{$domain}}/{{$item['id']}}/rest_change">
      @method('PUT')
      @csrf
      <input type="text" name="dummy" style="display:none;" / >
      @if(isset($origin))
        <input type="hidden" value="{{$origin}}" name="origin" />
      @endif
      @if(isset($manager_id))
        <input type="hidden" value="{{$manager_id}}" name="manager_id" />
      @endif
      <div class="row">
      @if(count($item["students"])>1)
        <div class="col-12">
          <div class="form-group">
            <label for="title" class="w-100">
              {{__('labels.students')}}
              <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
            </label>
            <select name="student_id" class="form-control select2" multiple="multiple" width=100% placeholder="{{__('labels.charge_student')}}" required="true" onChange="student_id_change()">
              @foreach($item["students"] as $member)
                @if($member->status!='rest' && $member->status!='lecture_cancel') @continue @endif
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
          <input type="hidden" name="student_id" value="{{ $member->user->student->id }}"
           member_id ="{{ $member->id }}"
           rest_type ="{{ $member->rest_type }}"
           rest_result ="{{ $member->rest_result }}"
           >
        @endforeach
      @endif
        <div class="col-12">
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
        <div class="col-12 mb-2">
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
        student_id_change();
      });
      function student_id_change(){
        console.log("student_id_change");
        var selecter = $("select[name='student_id'] option:selected");
        if(selecter.length==0) selecter = $("*[name='student_id']");
        var rest_type = selecter.attr('rest_type');
        var rest_result = selecter.attr('rest_result');
        $("input[name='rest_type'][value='"+rest_type+"']").iCheck('check');
        $("input[name='rest_result']").val(rest_result);
      }
      </script>
      <div class="row">
        <div class="col-12 col-md-6 mb-1">
            <button type="button" class="btn btn-submit btn-danger btn-block"  accesskey="{{$domain}}_action">
              {{__('labels.rest_change')}}
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
