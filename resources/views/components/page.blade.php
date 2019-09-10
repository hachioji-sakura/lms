@isset($action)
<div id="{{$domain}}_{{$action}}">
@else
<div id="{{$domain}}_">
@endisset

@if(!empty($page_message))
<div class="col-12 col-lg-12 col-md-12 mb-1">
  @if(isset($action) && $action=='delete')
    <h4 class="text-danger">{{$page_message}}</h4>
  @else
    <h6>{{$page_message}}</h6>
  @endif
</div>
@elseif(isset($action) && $action=='delete')
<div class="col-12 col-lg-12 col-md-12 mb-1">
  <h4 class="text-danger">削除してもよろしいですか？</h4>
</div>
@elseif(isset($action) && $action=='remind')
<div class="col-12 col-lg-12 col-md-12 mb-1">
  <h4 class="text-success">本登録依頼メールを送信しますがよろしいですか？</h4>
</div>
@endif
  {{-- 詳細表示項目を羅列する --}}
  @if(isset($field_logic))
    {{$field_logic}}
  @else
    @component('components.page_item', ['item' => $item, 'fields' => $fields, 'domain' => $domain])
    @endcomponent
  @endif


  @if(isset($forms) && !empty(trim($forms)))
    {{-- 独自formを利用する場合 --}}
    {{$forms}}
  @else
    {{-- 共通formを利用する場合 --}}
    {{-- action=deleteのみ、mothod=DELETE --}}
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
    @elseif(isset($action) && $action=='remind')
      @if(isset($item['email']))
      <div class="col-12" id="email_form">
        <div class="form-group">
          <label for="email">
            メールアドレス
            <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          </label>
          <div class="w-100 email-edited">
            <span>{{$item->email}}</span>
            <a href="javascript:void(0);" onClick="email_form_edit()" class="btn btn-sm btn-success ml-2"><i class="fa fa-edit"></i></a>
          </div>
          <div class="w-100 email-edit" style="display:none;">
            <input type="text" id="email" name="email" class="form-control w-50 float-left" placeholder="例：hachioji@sakura.com"  required="true" inputtype="email" query_check="users/email" query_check_error="このメールアドレスは登録済みです" value="{{$item['email']}}">
            <a href="javascript:void(0);" onClick="email_form_edited()" class="btn btn-sm btn-success float-left mt-1 ml-2"><i class="fa fa-check"></i></a>
          </div>
        </div>
      </div>
      <div class="col-12">
        <h6 class="text-sm p-1 pl-2 mt-2 bg-warning" >
          ※講師・事務を兼務する場合は、講師登録を先に行い<br>
          　講師一覧より、事務兼務にて設定します。
        </h6>
      </div>
      @endif
      <div class="col-12 col-lg-6 col-md-6 mb-1">
          <button type="button" class="btn btn-submit btn-success btn-block"  accesskey="{{$domain}}_{{$action}}" confirm="送信しますか？">
            <i class="fa fa-envelope mr-1"></i>
              送信する
          </button>
      </div>
      <div class="col-12 col-lg-6 col-md-6 mb-1">
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
      <div class="col-12 col-lg-6 col-md-6 mb-1">
          <button type="button" class="btn btn-submit btn-success btn-block"  accesskey="{{$domain}}_{{$action}}">
            <i class="fa fa-users-cog mr-1"></i>
              講師・事務の兼務設定する
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
  @endif
</div>
<script>
function email_form_edit(){
  $('div.email-edit').show();
  $('div.email-edited').hide();
}
function email_form_edited(){
  var email = $('div.email-edited span').text();
  var new_email = $('input[name="email"]').val();
  var is_check = false;
  console.log(email+'=='+new_email);
  if(email == new_email){
    is_check = true;
  }
  else if(front.validateFormValue('email_form')){
    is_check = true;
  }
  if(is_check){
    $('div.email-edit').hide();
    $('div.email-edited').show();
    $('div.email-edited span').html(new_email);
  }
  return is_check;
}

</script>
