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
          @if($key==="place")
            <label for="{{$key}}" class="w-100">
              {{$field['label']}}
            </label>
            <small title="{{$item["id"]}}" class="badge badge-success mt-1 mr-1">{{$item->place()}}</small>
          @elseif($key==='student_name' && ($action!='delete' || $item->is_group()!=true))
            <label for="{{$key}}" class="w-100">
              {{$field['label']}}
            </label>
            @foreach($item->students as $member)
              <a target="_blank" alt="student_name" href="/students/{{$member->user->details('students')->id}}" class="">
                <i class="fa fa-user-graduate mr-1"></i>
                {{$member->user->details('students')->name}}
              </a>
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
  @elseif(isset($action))
    @if($action!='delete')
      <form method="POST" action="/{{$domain}}/{{$item['id']}}/{{$action}}">
    @else
      <form method="POST" action="/{{$domain}}/{{$item['id']}}">
    @endif
      @csrf
      <input type="text" name="dummy" style="display:none;" / >
      <div class="row">
        {{-- 共通form用のボタン --}}
        @if(isset($action) && $action=='delete')
          @isset($trial_id)
          <input type="hidden" name="trial_id" value="{{$trial_id}}">
          @endisset
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
        @else
          <div class="col-12 mb-1">
            <a href="javascript:void(0);" data-dismiss="modal" role="button" class="btn btn-secondary btn-block float-left mr-1">
              <i class="fa fa-times-circle mr-1"></i>
              {{__('labels.close_button')}}
            </a>
          </div>
        @endif
      </div>
      </form>
  @elseif(isset($forms) && !empty(trim($forms)))
    {{$forms}}
  @endif


  @endslot

@endcomponent
