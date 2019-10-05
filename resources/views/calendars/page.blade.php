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
            <small title="{{$item["id"]}}" class="badge badge-{{config('status_style')[$item['status']]}} mt-1 mr-1">{{$item[$key]}}</small>
          @elseif($key==="place")
            <label for="{{$key}}" class="w-100">
              {{$field['label']}}
            </label>
            <small title="{{$item["id"]}}" class="badge badge-success mt-1 mr-1">{{$item->place()}}</small>
          @elseif($key==='student_name' && ($action!='delete' || $item->is_group()!=true))
            <label for="{{$key}}" class="w-100">
              {{$field['label']}}
            </label>
            @foreach($item["students"] as $member)
              <a target="_blank" alt="student_name" href="/students/{{$member->user->details('students')->id}}" class="text-{{config('status_style')[$member->status]}}">
                @if($member->status=='new')
                <i class="fa fa-question-circle mr-1"></i>
                @elseif($member->status=='confirm')
                <i class="fa fa-question-circle mr-1"></i>
                @elseif($member->status=='fix')
                <i class="fa fa-clock mr-1"></i>
                @elseif($member->status=='cancel')
                <i class="fa fa-ban mr-1"></i>
                @elseif($member->status=='presence')
                <i class="fa fa-check-circle mr-1"></i>
                @elseif($member->status=='absence')
                <i class="fa fa-calendar-times mr-1"></i>
                @elseif($member->status=='rest' || $member->status=='lecture_cancel')
                <i class="fa fa-user-times mr-1" title="{{$member->exchange_limit_date}}"></i>
                @endif
                {{$member->user->details('students')->name}}
                @if(isset($user) && ($user->role=="teacher" || $user->role=="manager") && !empty(trim($member->rest_result())))
                ({{$member->rest_result()}})
                @else
                <small title="{{$item["id"]}}" class="badge badge-{{config('status_style')[$member->status]}} mt-1 mr-1">{{$member->status_name()}}</small>
                @endif
              </a>
              {{-- TODO この処理は使わなくなったのでいずれ削除
              @if(isset($user) && $user->role=="manager")
                @if($member->rest_type=='a2')
                <a href="javascript:void(0);" onClick="rest_type_update({{$member->calendar_id}}, {{$member->id}}, 'a1');" class="btn btn-sm btn-default mr-2" title="休み２を休み１に変更します。">
                  <i class="fa fa-exchange-alt mr-1"></i>
                  休み１変更
                </a>
                @elseif($member->rest_type=='a1')
                <a href="javascript:void(0);" onClick="rest_type_update({{$member->calendar_id}}, {{$member->id}}, 'a2');" class="btn btn-sm btn-default mr-2"　title="休み１を、休み２に変更します。">
                  <i class="fa fa-exchange-alt mr-1"></i>
                  休み2変更
                </a>
                @endif
              @endif
              --}}
              {{--
              @if($action=='delete' && $item->is_group()==true && $member->status=='new')
              <a href="javascript:void(0);" onClick="member_delete({{$member->calendar_id}}, {{$member->id}});" class="ml-2 text-dark">
                <i class="fa fa-times"></i>
              </a>
              @endif
              --}}
              <br>
            @endforeach
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
  function rest_type_update(calendar_id, member_id, rest_type){
    var _req = {
      "rest_type" : rest_type
    };
    service.postAjax('/calendar_members/'+member_id+'/rest_type',_req,
    function(result, st, xhr) {
      if(result['status']===200){
        var d = (new Date()).getTime();
        var url = '/calendars/'+calendar_id+'?v='+d;
        console.log(url);
        base.showPage("dialog", "subDialog", "{{__('labels.details')}}", url);
      }
    },
    function(xhr, st, err) {
        messageCode = "error";
        messageParam= "\n"+err.message+"\n"+xhr.responseText;
        alert("休み種別変更エラー\n画面を再表示してください\n"+messageParam);
    }, "PUT");
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
        <table class="table table-striped w-80">
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
      <div class="col-12 col-lg-6 col-md-6 mb-1">
          <button type="button" class="btn btn-submit btn-danger btn-block"  accesskey="{{$domain}}_{{$action}}" confirm="削除しますか？">
            <i class="fa fa-trash mr-1"></i>
              削除する
          </button>
      </div>
      <div class="col-12 col-lg-6 col-md-6 mb-1">
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
