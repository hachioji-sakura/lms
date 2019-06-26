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
          @elseif($key==='student_name')
            <label for="{{$key}}" class="w-100">
              {{$field['label']}}
            </label>
            @foreach($item["students"] as $member)
              <a target="_blank" href="/students/{{$member->user->details('students')->id}}" class="text-{{config('status_style')[$member->status]}}">
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
                @elseif($member->status=='rest')
                <i class="fa fa-user-times mr-1"></i>
                @endif
                {{$member->user->details('students')->name}}
                @if(isset($user) && ($user->role=="teacher" || $user->role=="manager") && !empty(trim($member->remark())))
                 ({{$member->remark()}})
                @endif
              </a>
              {{--
              @if(isset($user) && ($user->role=="teacher" || $user->role=="manager"))
                @if($member->rest_type=='a2')
                <a href="javascript:void(0);" onClick="rest_type_update({{$member->calendar_id}}, {{$member->id}}, 'a1');" class="btn btn-sm btn-default mr-2">
                  <i class="fa fa-exchange-alt mr-1"></i>
                  休み１変更
                </a>
                @elseif($member->rest_type=='a1')
                <a href="javascript:void(0);" onClick="rest_type_update({{$member->calendar_id}}, {{$member->id}}, 'a2');" class="btn btn-sm btn-default mr-2">
                  <i class="fa fa-exchange-alt mr-1"></i>
                  休み2変更
                </a>
                @endif
              @endif
              --}}
              <br>
            @endforeach
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
  <script >
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
        base.showPage("dialog", "subDialog", '詳細', url);
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
    @if(isset($forms) && !empty(trim($forms)))
      {{$forms}}
    @endif
  @endslot

@endcomponent
