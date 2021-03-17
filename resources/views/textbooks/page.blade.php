<div id=
@isset($action)
 "{{$domain}}_{{$action}}"
@else
  "{{$domain}}_"
@endisset
>
@if(!empty($page_message))
<div class="col-12 my-1">
  @if(isset($action) && $action=='delete')
  <h5 class="text-danger">{{$page_message}}</h5>
  @else
    <h6>{{$page_message}}</h6>
  @endif
</div>
@elseif(isset($action) && $action=='delete')
<div class="col-12 my-1">
  <h5 class="text-danger">削除してもよろしいですか？</h5>
</div>
@endif
{{-- 詳細表示項目を羅列する --}}
@if(isset($field_logic))
{{$field_logic}}
@else
  @component('textbooks.page_item', ['item' => $item, 'fields' => $fields, 'domain' => $domain])
  @endcomponent
@endif

@if(isset($action) && $action!='delete')
<form method="POST" action="/{{$domain}}/{{$item['id']}}/{{$action}}">
@else
<form method="POST" action="/{{$domain}}/{{$item['id']}}">
@endif
  @csrf
  <input type="text" name="dummy" style="display:none;" / >
  <div class="row">
    {{-- 共通form用のボタン --}}
    @if(isset($action) && $action=='delete')
    @method('DELETE')
    <div class="col-12 col-md-6 my-1">
      <button type="button" class="btn btn-submit btn-danger btn-block"  accesskey="{{$domain}}_{{$action}}" confirm="削除しますか？">
        <i class="fa fa-trash mr-1"></i>
        削除する
      </button>
    </div>
    <div class="col-12 col-md-6 my-1">
      <a href="javascript:void(0);" data-dismiss="modal" role="button" class="btn btn-secondary btn-block float-left mr-1">
        <i class="fa fa-times-circle mr-1"></i>
        キャンセル
      </a>
    </div>
    @elseif(isset($action) && $action=='to_manager')
      @if(isset($manager))
      <div class="col-12">
        <div class="form-group">
          <label for="gender">
            同姓同名の事務員が存在しますが、紐づけをしますか？
          </label>
          <div class="input-group">
            <div class="form-check">
              <input class="form-check-input icheck flat-green" type="radio" name="already_manager_id" id="already_manager_1" value="{{$manager->id}}" required="true">
              <label class="form-check-label" for="already_manager_1">
                {{$manager->name()}}(ID={{$manager->id}})に紐づけする
              </label>
            </div>
            <div class="form-check ml-2">
              <input class="form-check-input icheck flat-green" type="radio" name="already_manager_id" id="already_manager_0" value="0" required="true">
              <label class="form-check-label" for="already_manager_0">
                事務を新規登録する
              </label>
            </div>
          </div>
        </div>
      </div>
      @endif
      <div class="col-12 col-md-6 my-1">
        <button type="button" class="btn btn-submit btn-success btn-block"  accesskey="{{$domain}}_{{$action}}">
          <i class="fa fa-users-cog mr-1"></i>
          講師・事務の兼務設定する
        </button>
      </div>
      <div class="col-12 col-md-6 my-1">
        <a href="javascript:void(0);" data-dismiss="modal" role="button" class="btn btn-secondary btn-block float-left mr-1">
          <i class="fa fa-times-circle mr-1"></i>
          キャンセル
        </a>
      </div>
    @else
    <div class="col-12 my-1">
      <a href="javascript:void(0);" data-dismiss="modal" role="button" class="btn btn-secondary btn-block float-left mr-1">
        <i class="fa fa-times-circle mr-1"></i>
        {{__('labels.close_button')}}

      </a>
    </div>
    @endif
  </div>
</form>
</div>
