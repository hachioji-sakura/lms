@extends('layouts.loginbox')
@section('title')
@if($item->trial_id > 0)体験@endif授業予定
@if($subpage==="rest")
  お休み連絡
@elseif($item['status']==="confirm")
  のご確認
@endif
@endsection

@section('title_header')
  @if($item->trial_id > 0)体験@endif授業予定
  @if($subpage==="rest")
    お休み連絡
  @elseif($item['status']==="confirm")
    のご確認
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
          @if(strtotime(date('Y/m/d H:i:s')) >= strtotime($item["date"].' 09:00:00') && $item['trial_id'] == 0)
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
            @component('calendars.forms.fix_form', ['item' => $item, 'user'=>$user]) @endcomponent
            @component('calendars.forms.target_member', ['item' => $item, 'user'=>$user, 'status'=>'fix']) @endcomponent
          </div>
          <div class="row">
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
              @component('calendars.forms.rest_form', ['item' => $item, 'user'=>$user]) @endcomponent
              @component('calendars.forms.target_member', ['item' => $item, 'user'=>$user, 'status'=>'rest']) @endcomponent
            </div>
          <div class="row">
            <div class="col-12">
                <button type="button" class="btn btn-submit btn-danger btn-block"  accesskey="_form">
                  <i class="fa fa-envelope mr-1"></i>
                    休み連絡
                </button>
            </div>
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
