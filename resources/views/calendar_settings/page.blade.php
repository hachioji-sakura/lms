@component('components.page', ['item' => $item, 'fields' => $fields, 'domain' => $domain, 'action' => $action])
  {{-- メッセージカスタマイズ --}}
  @slot('page_message')
    @if(isset($page_message) && !empty(trim($page_message)))
      {{$page_message}}
    @endif
  @endslot
  {{-- 表示部分カスタマイズ --}}
  @slot('field_logic')
  <div class="row">
    @foreach($fields as $key=>$field)
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
            <small title="id={{$item["id"]}},work={{$item["work"]}}" class="badge badge-{{config('status_style')[$item['status']]}} mt-1 mr-1">{{$item[$key]}}</small>
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
          @elseif($key==='teacher_name')
          <label for="{{$key}}" class="w-100">
            {{$field['label']}}
          </label>
            @if($user->role=='manager') <a href="/teachers/{{$item->user->teacher->id}}" target="_blank"> @endif
            {{$item[$key]}}
            @if($item->is_online()==true && $item->user->has_tag('skype_name')==true)({{__('labels.skype_name')}}:{{$item->user->get_tag_value('skype_name')}})@endif
            @if($user->role=='manager') </a> @endif
          @elseif($key==='student_name' && ($action!='delete' || $item->is_group()!=true))
            <label for="{{$key}}" class="w-100">
              {{$field['label']}}
            </label>
            @component('calendars.forms.label_students', ['item' => $item, 'user'=>$user, 'set_br' => true , 'status_visible'=> true]) @endcomponent
          @elseif(isset($item[$key]) && gettype($item[$key])=='array')
            <label for="{{$key}}" class="w-100">
              {{$field['label']}}
            </label>
            @foreach($item[$key] as $_item)
            <span class="text-xs mx-2">
              <small class="badge badge-primary mt-1 mr-1">
                {{$_item}}
              </small>
            </span>
            @endforeach
          @else
          <label for="{{$key}}" class="w-100">
            {{$field['label']}}
          </label>
          {{$item[$key]}}
          @endif
        </div>
    </div>
  @endforeach
  </div>
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
        <table class="table w-80">
          <tr class="bg-gray">
            <th class="p-1 pl-2 text-sm "><i class="fa fa-user mr-1"></i>{{__('labels.students')}}</th>
            <th class="p-1 pl-2 text-sm"><i class="fa fa-trash mr-1"></i>
              <label class="mx-2">
                {{__('labels.delete')}}
              </label>
              <input class="form-check-input icheck flat-red ml-2" type="checkbox" name="all_calendar_member_delete" value="delete" onChange='all_calendar_member_delete_check();' >
            </th>
          </tr>
          @foreach($item["students"] as $member)
          @if($member->user->details()->role==="student")
          <tr class="">
            <th class="p-1 pl-2">
              {{$member->user->details()->name}}
            </th>
            <td class="p-1 text-sm text-center">
              <div class="input-group">
                <div class="form-check">
                  <input class="form-check-input icheck flat-red calendar_member_delete" type="checkbox" name="{{$member->id}}_delete" id="{{$member->id}}_delete" value="delete" >
                  <label class="form-check-label" for="{{$member->id}}_delete"></label>
                </div>
              </div>
            </td>
          </tr>
          @endif
          @endforeach
        </table>
      </div>
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
    </script>
    <div class="row">
      @method('DELETE')
      <div class="col-12 col-md-6 mb-1">
          <button type="button" class="btn btn-submit btn-danger btn-block"  accesskey="{{$domain}}_{{$action}}" confirm="削除しますか？">
            <i class="fa fa-trash mr-1"></i>
              削除する
          </button>
      </div>
      <div class="col-12 col-md-6 mb-1">
        <a href="javascript:void(0);" data-dismiss="modal" role="button" class="btn btn-secondary btn-block float-left mr-1">
          <i class="fa fa-times-circle mr-1"></i>
          キャンセル
        </a>
      </div>
    </div>
    @elseif($action=='dummy_release')
    <form method="POST" action="/{{$domain}}/{{$item['id']}}/status_update/new">
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
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
