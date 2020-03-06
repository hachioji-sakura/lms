@component('components.page', ['item' => $item, 'fields' => $fields, 'domain' => $domain, 'action'=>$action])
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
          <small title="id={{$item["id"]}},work={{$item["work"]}}" class="badge badge-{{config('status_style')[$item['status']]}} mt-1 mr-1">{{$item[$key]}}</small>
        @elseif($key==="place")
          <label for="{{$key}}" class="w-100">
            {{$field['label']}}
          </label>
          <small title="{{$item["id"]}}" class="badge badge-success mt-1 mr-1">{{$item->place()}}</small>
        @elseif($key==='student_name' && ($action!='delete' || $item->is_group()!=true))
          <label for="{{$key}}" class="w-100">
            {{$field['label']}}
          </label>
          @component('calendars.forms.label_students', ['item' => $item, 'user'=>$user, 'set_br' => true , 'status_visible'=> true]) @endcomponent
        @elseif($key==='teaching_name')
        <label for="{{$key}}" class="w-100">
          {{$field['label']}}
        </label>
        {{$item[$key]}}
        @if($item->exchanged_calendar_id > 0)
        <br>
        <small class="badge badge-primary mt-1 mr-1 p-1">
          <i class="fa fa-exchange-alt mr-1"></i>
          {{__('labels.exchange')}}: <span id="exchanged_calendar_datetime">
            {{$item->exchanged_calendar->details()["datetime"]}}
          </span>
        </small>
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
        {{$item[$key]}}
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
              <small title="{{$item["id"]}}" class="ml-2 badge badge-{{config('status_style')[$member->status]}} mt-1 mr-1">{{$member->status_name()}}</small>
            </th>
            <td class="p-1 text-sm text-center">
              @if($member->status!="new" )
                <i class="fa fa-times mr-1"></i>
              @else
              <div class="input-group">
                <div class="form-check">
                  <input class="form-check-input icheck flat-red calendar_member_delete" type="checkbox" name="{{$member->id}}_delete" id="{{$member->id}}_delete" value="delete" required="true" >
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
    @elseif(isset($forms) && !empty(trim($forms)))
      {{$forms}}
    @endif
  @endslot

@endcomponent
