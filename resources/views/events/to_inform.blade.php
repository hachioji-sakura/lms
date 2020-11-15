@component('components.page', ['item' => $item, 'fields' => $fields, 'domain' => $domain, 'action'=>$action])
{{-- メッセージカスタマイズ --}}
  @slot('page_message')
    <div class="alert alert-primary">
      下記の内容にて、送信対象者にメールを送信します。
    </div>
  @endslot

  {{-- フォーム部分カスタマイズ --}}
  @slot('forms')
    <form method="POST" action="/{{$domain}}/{{$item['id']}}/to_inform">
      @csrf
     <input type="text" name="dummy" style="display:none;" / >
     <div class="row">
      <div class="col-12">
        <label for="" class="w-100">
          送信対象
        </label>
        <table class="table w-80" id="send_user_list">
          <tr class="bg-gray">
            <th class="p-1 pl-2 text-sm "><i class="fa fa-user mr-1"></i>{{__('labels.students')}}</th>
            <th class="p-1 pl-2 text-sm"><i class="fa fa-check mr-1"></i>
              <label class="mx-2">
                {{__('labels.select')}}
              </label>
              <input class="form-check-input icheck flat-blue ml-2" type="checkbox" accesskey="select_send_user_ids[]" value="1" onChange='all_checked_change(this);' validate="is_checked_exist('send_user_list', 'select_send_user_ids[]');">
            </th>
          </tr>
          @foreach($item->event_users as $event_user)
          <tr class="">
            <th class="p-1 pl-2">
              {{$event_user->user_name}}
              <small title="{{$item["id"]}}" class="ml-2 badge badge-{{config('status_style')[$event_user->status]}} mt-1 mr-1">{{$event_user->status_name}}</small>
            </th>
            <td class="p-1 text-sm text-center">

              <div class="input-group">
                <div class="form-check">
                  <input class="form-check-input icheck flat-blue" type="checkbox" name="select_send_user_ids[]" id="select_send_user_id_{{$event_user->id}}" value="{{$event_user->id}}"
                  @if($event_user->status=='new')
                   checked
                  @endif
                  >
                  <label class="form-check-label" for="select_send_user_id_{{$event_user->id}}">{{__('labels.select')}}</label>
                </div>
              </div>

            </td>
          </tr>
          @endforeach
        </table>
      </div>
    </div>
    <div class="row">
      <div class="col-12 col-md-6 mb-1">
          <button type="button" class="btn btn-submit btn-primary btn-block"  accesskey="{{$domain}}_{{$action}}" confirm="送信しますか？">
            <i class="fa fa-envelope mr-1"></i>
              {{__('labels.send_button')}}
          </button>
      </div>
      <div class="col-12 col-md-6 mb-1">
        <a href="javascript:void(0);" data-dismiss="modal" role="button" class="btn btn-secondary btn-block float-left mr-1">
          <i class="fa fa-times-circle mr-1"></i>
          {{__('labels.cancel_button')}}
        </a>
      </div>
    </div>

  @endslot

@endcomponent
