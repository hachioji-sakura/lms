@extends('layouts.loginbox')
@section('title')
@if($item->trial_id > 0)体験@endif
@if($item['status']==="confirm")
  のご確認
@elseif($item['status']==="fix")
  お休み連絡
@endif
@endsection
@section('title_header')
  @if($item->trial_id > 0)体験@endif授業予定
  @if($item['status']==="confirm")
    のご確認
  @elseif($item['status']==="fix" && $subpage==="rest")
    お休み連絡
  @endif
@endsection
@section('content')
  @component('components.page', ['item' => $item, 'fields' => $fields, 'domain' => $domain])
    @slot('page_message')
      @if(!empty($result))
        <h4 class="bg-success p-3 text-sm">
          {{$result}}
        </h4>
      @else
        @if($item['status']==="fix" && $subpage==="rest")
          @if(strtotime(date('Y/m/d H:i:s')) >= strtotime($item["date"].' 09:00:00'))
            {{-- 授業当日9時を過ぎたら休み連絡はできない --}}
            <div class="col-12 col-lg-12 col-md-12 mb-1 bg-warning p-4">
              <i class="fa fa-exclamation-triangle mr-2"></i>この休み連絡は、振替対象外となります。
              <br>
              <span class="text-sm">※授業当日のAM9:00以降の連絡</span>
            </div>
          @else
            この授業予定をお休みしますか？
          @endif
        @else
          以下の授業予定をご確認してください
        @endif
      @endif
    @endslot

    @slot('forms')
      @if(empty($result))
      <form method="POST" action="/calendars/{{$item['id']}}" id="_form">
        @if($item['status']==="confirm" && $subpage==="fix")
          @csrf
          @method('PUT')
          <input type="hidden" value="{{$user->user_id}}" name="user" />
          <div class="row">
            <div class="col-12 mb-1">
              <div class="form-group">
                <label for="status">
                  この授業予定に出席する
                  <span class="right badge badge-danger ml-1">必須</span>
                </label>
                <div class="input-group">
                  <div class="form-check">
                      <input class="form-check-input icheck flat-green" type="radio" name="status" id="status_fix" value="fix" required="true" onChange="status_radio_change()">
                      <label class="form-check-label" for="status_fix">
                          はい
                      </label>
                  </div>
                  <div class="form-check ml-2">
                      <input class="form-check-input icheck flat-green" type="radio" name="status" id="status_cancel" value="cancel" required="true"  onChange="status_radio_change()">
                      <label class="form-check-label" for="status_cancel">
                          いいえ
                      </label>
                  </div>
                </div>
              </div>
            </div>
            <script>
            function status_radio_change(obj){
              var is_cancel = $('input[type="radio"][name="status"][value="cancel"]').prop("checked");
              if(is_cancel){
                $("textarea[name='remark']").show();
                $("#cancel_reason").collapse("show");
              }
              else {
                $("textarea[name='remark']").hide();
                $("#cancel_reason").collapse("hide");
              }
            }
            </script>
            <div class="col-12 collapse" id="cancel_reason">
              <div class="form-group">
                <label for="howto" class="w-100">
                  授業予定に参加できない理由をお知らせください
                  <span class="right badge badge-danger ml-1">必須</span>
                </label>
                <textarea type="text" name="remark" class="form-control" placeholder="例：予定日時の都合があわなくなり、X月X日 15時～に変更したい。" required="true"></textarea>
              </div>
            </div>
            <div class="col-12 mb-1">
                <button type="button" class="btn btn-submit btn-info btn-block"  accesskey="_form">
                  <i class="fa fa-envelope mr-1"></i>
                  送信
                </button>
              </form>
            </div>
          </div>
        @elseif($item['status']==="fix" && $subpage==="rest")
            @csrf
            @method('PUT')
            <input type="hidden" value="{{$user->user_id}}" name="user" />
            <input type="hidden" value="{{$token}}" name="access_key" />
            <input type="hidden" value="rest" name="status" />
          <div class="row">
            @if(strtotime(date('Y/m/d H:i:s')) >= strtotime($item["date"].' 09:00:00'))
            <div class="col-12 mt-2 mb-1">
              <div class="form-group">
                <input class="form-check-input icheck flat-green" type="checkbox" id="agreement" name="agreement" value="1" required="true" >
                <label class="form-check-label" for="agreement">
                  振替対象外となることを確認しました。
                </label>
              </div>
            </div>
            @endif
            <div class="col-12" id="cancel_reason">
              <div class="form-group">
                <label for="howto" class="w-100">
                  お休みの理由をお知らせください
                  <span class="right badge badge-danger ml-1">必須</span>
                </label>
                <textarea type="text" name="remark" class="form-control" placeholder="例：予定日時の都合があわなくなり、X月X日 15時～に変更したい。" required="true"></textarea>
              </div>
            </div>
            <div class="col-12">
                <button type="button" class="btn btn-submit btn-danger btn-block"  accesskey="_form">
                  <i class="fa fa-envelope mr-1"></i>
                    休み連絡
                </button>
            </div>
        @endif
      </form>
      @endif
      @if($item['trial_id'] == 0)
      <div class="row mt-2">
        <div class="col-12">
          <a href="/login" role="button" class="btn btn-outline-success btn-block btn-sm float-left mr-1">
            <i class="fa fa-sign-in-alt mr-1"></i>ログイン画面へ
          </a>
        </div>
      </div>
      @else
      　
      @endif
    @endslot
  @endcomponent
  <script>
  $(function(){
    base.pageSettinged("_form", null);
    //submit
    $("button.btn-submit").on('click', function(e){
      e.preventDefault();
      if(front.validateFormValue('_form')){
        $("form").submit();
      }
    });
  });
  </script>
@endsection
