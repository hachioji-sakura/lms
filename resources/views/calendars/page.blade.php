@component('components.page', ['item' => $item, 'fields' => $fields, 'domain' => $domain, 'action'=>$action])
{{-- メッセージカスタマイズ --}}
  @slot('page_message')
    @if(isset($page_message) && !empty(trim($page_message)))
      {{$page_message}}
    @elseif(isset($warning_message) && !empty(trim($warning_message)))
    <div class="col-12 mb-2 bg-warning p-4">
      {!!$warning_message!!}
    </div>
    @endif
  @endslot
  {{-- 表示部分カスタマイズ --}}
  @slot('field_logic')
  <div class="row">
  @foreach($fields as $key=>$field)
    @if(!(isset($user) && ($user->role=="teacher" || $user->role=="manager")) && $key=="remark") @continue @endif
    @if(isset($field['size']))
    <div class="col-{{$field['size']}}">
    @else
    <div class="col-12">
    @endif
      <div class="form-group">
        @if($key==="status_name")
          <label for="{{$key}}" class="w-100">
            {{$field['label']}}
          </label>
          <small title="id={{$item["id"]}}" class="badge badge-{{config('status_style')[$item['status']]}} mt-1 mr-1">{{$item[$key]}}</small>
        @elseif($key==="place_floor_name")
          <label for="{{$key}}" class="w-100">
            {{$field['label']}}
          </label>
          @if(!(isset($user) && ($user->role=="teacher" || $user->role=="manager")))
            @if($item->is_online()==true)
            <small class="badge badge-info mt-1 mr-1 text-sm">
              <i class="fa fa-globe">{{__('labels.online')}}</i>
            </small>
            @else
              <small class="badge badge-success mt-1 mr-1">{{$item[$key]}}</small>
            @endif
          @else
            <small class="badge badge-success mt-1 mr-1">{{$item[$key]}}</small>
            @if($item->is_online()==true)
            <small class="badge badge-info mt-1 mr-1 text-sm">
              <i class="fa fa-globe">{{__('labels.online')}}</i>
            </small>
            @endif
          @endif
        @elseif($key==='student_name' && ($action!='delete' || $item->is_group()!=true) && isset($item['students']))
          <label for="{{$key}}" class="w-100">
            {{$field['label']}}
          </label>
          @component('calendars.forms.label_students', ['item' => $item, 'user'=>$user, 'set_br' => true , 'status_visible'=> true]) @endcomponent
        @elseif($key==='teacher_name')
        <label for="{{$key}}" class="w-100">
          {{$field['label']}}
        </label>
          @if($user->role=='manager') <a href="/teachers/{{$item->user->teacher->id}}" target="_blank"> @endif
          {{$item[$key]}}
          @if($item->is_online()==true && $item->user->has_tag('skype_name')==true)({{__('labels.skype_name')}}:{{$item->user->get_tag_value('skype_name')}})@endif
          @if($user->role=='manager') </a> @endif
        @elseif($key==='teaching_name' || $key==='course')
        <label for="{{$key}}" class="w-100">
          {{$field['label']}}
        </label>
        {{$item[$key]}}
        @if($item->exchanged_calendar_id > 0)
        <br>
        <small class="badge badge-primary mt-1 mr-1 p-1">
          <i class="fa fa-exchange-alt mr-1"></i>
          {{__('labels.exchange')}}: <span id="exchanged_calendar_datetime">
            @if(isset($item->exchanged_calendar))
            {{$item->exchanged_calendar->details()["datetime"]}}
            @endif
          </span>
        </small>
        @else
        @endif
        <br>

        @elseif(isset($item[$key]) && gettype($item[$key])=='array')
          <label for="{{$key}}" class="w-100">
            {{$field['label']}}
          </label>
          @foreach($item[$key] as $_item)
          <span class="text-xs">
            <small class="badge badge-primary mt-1 mr-1">
              {{$_item}}
            </small>
          </span>
          @endforeach
        @else
        <label for="{{$key}}" class="w-100">
          {{$field['label']}}
        </label>
        {!!nl2br($item[$key])!!}
        @endif
      </div>
    </div>
  @endforeach
  </div>

  <script >
  function all_calendar_member_delete_check(){
    var obj = $('input[name="all_calendar_member_delete"]');
    var _val = obj.val();
    var _checked = obj.prop('checked');
    if(_val=='delete'){
      if(_checked){
        $('input[type="checkbox"].calendar_member_delete').each(function(i, e){
          $(this).iCheck('check');
        });
      }
      else{
        $('input[type="checkbox"].calendar_member_delete').each(function(i, e){
          $(this).iCheck('uncheck');
        });
      }
    }
  }
  function member_delete_validate(){
    var _ret = false;
    $('input[type="checkbox"].calendar_member_delete').each(function(i, e){
      var _checked = $(this).prop('checked');
      if(_checked) _ret = true;
      return ;
    });
    if(_ret==false){
      console.log('error');
      front.showValidateError($("#delete_member_list"), "{{__('messages.error_no_checked')}}");
      return false;
    }
    return true;
  }
  </script>
  @endslot
  {{-- フォーム部分カスタマイズ --}}
  @slot('forms')
    @if($action=='delete' && $item->is_group()==true)
      <form method="POST" action="/{{$domain}}/{{$item['id']}}">
      @csrf
     <input type="text" name="dummy" style="display:none;" / >
     <div class="row">
      <div class="col-12">
        <label for="member_delete" class="w-100">
          参加生徒削除
        </label>
        <table class="table w-80" id="delete_member_list">
          <tr class="bg-gray">
            <th class="p-1 pl-2 text-sm "><i class="fa fa-user mr-1"></i>{{__('labels.students')}}</th>
            <th class="p-1 pl-2 text-sm"><i class="fa fa-trash mr-1"></i>
              <label class="mx-2">
                {{__('labels.delete')}}
              </label>
              <input class="form-check-input icheck flat-red ml-2" type="checkbox" name="all_calendar_member_delete" value="delete" onChange='all_calendar_member_delete_check();' validate = "member_delete_validate();">
            </th>
          </tr>
          @foreach($item["students"] as $member)
          @if($member->user->details()->role==="student")
          <tr class="">
            <th class="p-1 pl-2">
              {{$member->user->details()->name}}
              <small title="{{$item["id"]}}" class="ml-2 badge badge-{{config('status_style')[$member->status]}} mt-1 mr-1">{{$member->status_name()}}</small>
            </th>
            <td class="p-1 text-sm text-center">
              @if($member->is_last_status()==true )
                <i class="fa fa-times mr-1"></i>
              @else
              <div class="input-group">
                <div class="form-check">
                  <input class="form-check-input icheck flat-red calendar_member_delete" type="checkbox" name="{{$member->id}}_delete" id="{{$member->id}}_delete" value="delete">
                  <label class="form-check-label" for="{{$member->id}}_delete"></label>
                </div>
              </div>
              @endif
            </td>
          </tr>
          @endif
          @endforeach
        </table>
      </div>
    </div>
    <div class="row">
      @method('DELETE')
      <div class="col-12 col-md-6 mb-1">
          <button type="button" class="btn btn-submit btn-danger btn-block"  accesskey="{{$domain}}_{{$action}}" confirm="削除しますか？">
            <i class="fa fa-trash mr-1"></i>
              {{__('labels.delete_button')}}
          </button>
      </div>
      <div class="col-12 col-md-6 mb-1">
        <a href="javascript:void(0);" data-dismiss="modal" role="button" class="btn btn-secondary btn-block float-left mr-1">
          <i class="fa fa-times-circle mr-1"></i>
          {{__('labels.cancel_button')}}
        </a>
      </div>
    </div>
    @elseif($action=='dummy_release')
    <form method="POST" action="/{{$domain}}/{{$item['id']}}/status_update/new">
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    @if($item->is_online()==true && empty($item->user->teacher->get_tag_value('skype_name')))
    <div class="row">
      <div class="col-12 mb-1">
        <div class="form-group">
          <input class="form-check-input icheck flat-red" type="checkbox" id="skype_name_check" name="skype_name_check" value="1" required="true">
          <label class="form-check-label" for="skype_name_check">
            <i class="fa fa-exclamation-triangle mr-1"></i>講師のSkype名が設定されていないことを確認しました
          </label>
        </div>
      </div>
    </div>
    @endif
    @if($item->is_first_place()==true)
    <div class="row">
      <div class="col-12 mb-1">
      <div class="form-group">
        <input class="form-check-input icheck flat-red" type="checkbox" id="first_place_check" name="first_place_check" value="1" required="true">
        <label class="form-check-label" for="first_place_check">
          <i class="fa fa-exclamation-triangle mr-1"></i>{{$item->place_floor->place->name()}}の鍵を持っていることを確認しました
        </label>
      </div>
    </div>
    </div>
    @endif
    <div class="row">
      @method('PUT')
      <div class="col-12 col-md-6 mb-1">
          <button type="button" class="btn btn-submit btn-primary btn-block"  accesskey="{{$domain}}_{{$action}}" confirm="ダミー解除し、講師に予定の連絡をしますか？">
            <i class="fa fa-unlock-alt mr-1"></i>
              {{__('labels.dummy_release')}}
          </button>
      </div>
      <div class="col-12 col-md-6 mb-1">
        <a href="javascript:void(0);" data-dismiss="modal" role="button" class="btn btn-secondary btn-block float-left mr-1">
          <i class="fa fa-times-circle mr-1"></i>
          {{__('labels.cancel_button')}}
        </a>
      </div>
    </div>
    @elseif(isset($forms) && !empty(trim($forms)))
      {{$forms}}
    @endif
  @endslot

@endcomponent
