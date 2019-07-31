<?php
  $page_message = "";
  if($trial_id>0){
    $page_message = "この体験申し込みの生徒を、設定から削除しますか？";
  }
?>
@component('components.page', ['item' => $item, 'fields' => $fields, 'domain' => $domain, 'action' => $action, 'page_message' => $page_message])
{{-- メッセージカスタマイズ --}}
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
          @elseif($key==='student_name')
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
    {{-- 共通formを利用する場合 --}}
    {{-- action=deleteのみ、mothod=DELETE --}}
    @if(isset($action) && $action!='delete')
      <form method="POST" action="/{{$domain}}/{{$item['id']}}/{{$action}}">
    @else
      <form method="POST" action="/{{$domain}}/{{$item['id']}}">
    @endif
    @csrf
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
  @endslot

@endcomponent
