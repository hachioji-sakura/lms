@component('components.page', ['item' => $item, 'fields' => $fields, 'domain' => $domain, 'action'=>$action])
{{-- メッセージカスタマイズ --}}
  @slot('page_message')
  <div class="col-12">
    <div class="alert alert-warning text-sm pr-2 schedule_type schedule_type_class">
      下記の内容にて、対象者にメールを送信します。
    </div>
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
