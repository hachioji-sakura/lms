@extends('layouts.loginbox')
@section('title', $page_title)
@section('title_header', $page_title)

@section('content')
  @component($domain.'.page', ['user'=>$user, 'item' => $item, 'fields' => $fields, 'domain' => $domain, 'action' => ''])
    @slot('page_message')
      @if(!empty($result))
        <h4 class="bg-success p-3 text-sm">
          {{$result}}
        </h4>
      @else
        @if($item['status']==="fix" && $subpage==="rest")
          @if($item->is_prev_rest_contact()==false && $item['trial_id'] == 0)
            {{-- 授業当日9時を過ぎたら休み連絡はできない --}}
            <div class="col-12 mb-1 bg-warning p-4">
              <i class="fa fa-exclamation-triangle mr-2"></i>この休み連絡は、振替対象外となります。
              <br>
              <span class="text-sm">※授業当日のAM9:00以降の連絡</span>
            </div>
          @else
          <div class="col-12 mb-1 bg-success p-2">
            <i class="fa fa-exclamation-triangle mr-2"></i>{{__('messages.confirm_rest_contact')}}
          </div>
          @endif
        @else
          @if($item->is_passed()==true)
          <div class="col-12 bg-danger p-2 mb-2">
            <i class="fa fa-exclamation-triangle mr-1"></i>{!!nl2br(__('messages.error_passed_calendar'))!!}
          </div>
          @else
          <div class="col-12 mb-1 bg-success p-2">
            <i class="fa fa-exclamation-triangle mr-2"></i>{{__('messages.info_calendar_confirm')}}
          </div>
          @endif
        @endif
      @endif
    @endslot

    @slot('forms')
      @if(empty($result))
      <form method="POST" action="/{{$domain}}/{{$item['id']}}" id="_form">
        @if(($item['status']==="confirm" || $item['status']==="fix") && $subpage==="fix")
          @csrf
		      <input type="text" name="dummy" style="display:none;" / >
          @method('PUT')
          @if($item->is_passed()==true)
          @else
          <input type="hidden" value="{{$user->user_id}}" name="user" />
          <input type="hidden" value="fix" name="status" />
          <div class="row">
            @component('calendars.forms.fix_form', ['item' => $item, 'user'=>$user]) @endcomponent
            @component('calendars.forms.target_member', ['item' => $item, 'user'=>$user, 'status'=>'fix']) @endcomponent
          </div>
          <div class="row">
            <div class="col-12 mb-1">
                <button type="button" class="btn btn-submit btn-info btn-block"  accesskey="_form" confirm="{{__('messages.confirm_schedule_to_confirm')}}">
                  <i class="fa fa-envelope mr-1"></i>
                  {{__('labels.send_button')}}
                </button>
              </form>
            </div>
          </div>
          @endif
        @elseif($item['status']==="fix" && $subpage==="rest")

          @csrf
          <input type="text" name="dummy" style="display:none;" / >
          @method('PUT')
          <input type="hidden" value="{{$user->user_id}}" name="user" />
          <input type="hidden" value="{{$token}}" name="access_key" />
          <input type="hidden" value="rest" name="status" />
          <div class="row">
            @component('calendars.forms.rest_form', ['item' => $item, 'user'=>$user]) @endcomponent
            @component('calendars.forms.target_member', ['item' => $item, 'user'=>$user, 'status'=>'rest']) @endcomponent
          </div>
          <div class="row">
            <div class="col-12 mb-6">
              <label for="course_minutes" class="w-100">
                休み理由
                <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
              </label>
              <input type="text" name="rest_result" class="form-control" placeholder="" inputtype="zenkaku">
            </div>
           　
          </div>
          <div class="row">
            <div class="col-12">
                <button type="button" class="btn btn-submit btn-danger btn-block"  accesskey="_form" confirm="{{__('messages.confirm_rest_contact')}}">
                  <i class="fa fa-envelope mr-1"></i>
                    {{__('labels.rest_contact')}}
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
            <i class="fa fa-sign-in-alt mr-1"></i>{{__('labels.to_login')}}
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
        $(this).prop("disabled",true);
        $("form").submit();
      }
    });
  });
  </script>
@endsection
