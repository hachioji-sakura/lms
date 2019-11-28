@component('calendars.page', ['item' => $item, 'fields' => $fields, 'action'=>$action, 'domain' => $domain, 'user'=>$user])
  @slot('page_message')
    @if($item->is_management()==false)
      @if(strtotime(date('Y/m/d H:i:s')) >= strtotime($item["date"].' 09:00:00'))
        {{-- 授業当日9時を過ぎたら休み連絡はできない
        <div class="col-12 col-lg-12 col-md-12 mb-1">
          <h4 class="text-danger">授業当日AM9:00以降の休み連絡はできません。</h4>
        </div>
        --}}
      @else
      @endif
      @if($user->role==="manager" || $user->role==="teacher")
      <div class="col-12 col-lg-12 col-md-12 bg-danger p-2 mb-2">
        <i class="fa fa-exclamation-triangle mr-1"></i>生徒の代わりに連絡をします。
      </div>
      @endif
      この授業予定をお休みしますか？
    @else
      この勤務予定をお休みしますか？
    @endif
  @endslot
  @slot('forms')
  <div id="{{$domain}}_action">
    @if(count($item["students"]) > 1)
    {{-- グループレッスン系 （生徒複数） --}}
    <form method="POST" action="/calendars/{{$item['id']}}">
      @csrf
      <input type="text" name="dummy" style="display:none;" / >
      @method('PUT')
      <input type="hidden" value="rest" name="status" />

      <div class="row" id="rest_list">
        <div class="col-12">
          <table class="table table-striped w-80" id="rest_list_table">
            <tr class="bg-gray">
              <th class="p-1 pl-2 text-sm "><i class="fa fa-user mr-1"></i>生徒</th>
              <th class="p-1 pl-2 text-sm"><i class="fa fa-check mr-1"></i>休み</th>
            </tr>
            @foreach($item["students"] as $member)
            @if($member->user->details()->role==="student")
            <tr class="">
              <th class="p-1 pl-2">
                {{$member->user->details()->name}}</th>
              <td class="p-1 text-sm text-center">
                @if($member->status=="rest")
                  <i class="fa fa-times mr-1"></i>お休み
                @elseif($member->status=="fix")
                <div class="input-group">
                  <div class="form-check">
                    <input class="form-check-input icheck flat-green rest_check" type="checkbox" name="{{$member->id}}_status" id="{{$member->id}}_status_rest" value="rest" validate="status_rest_check()">
                    <label class="form-check-label" for="{{$member->id}}_status_rest">
                        休み
                    </label>
                  </div>
                </div>
                @endif
              </td>
            </tr>
            @endif
            @endforeach
          </table>
          <script>
          function status_rest_check(){
            console.log("status_rest_check");
            var _is_scceuss = false;
            $("input.rest_check[type='checkbox']:checked").each(function(index, value){
              var val = $(this).val();
              console.log(val);
              if(val=="rest"){
                _is_scceuss = true;
              }
            });
            if(!_is_scceuss){
              front.showValidateError('#rest_list_table', '休みにチェックが入っていません');
            }
            return _is_scceuss;
          }
          </script>

        </div>
      </div>
      <div class="row">
        <div class="col-12 col-md-6 mb-1">
            <button type="button" class="btn btn-submit btn-danger btn-block"  accesskey="{{$domain}}_action">
              <i class="fa fa-envelope mr-1"></i>
              {{__('labels.rest_contact')}}
            </button>
        </div>
        <div class="col-12 col-md-6 mb-1">
            <button type="reset" class="btn btn-secondary btn-block">
              {{__('labels.close_button')}}
            </button>
        </div>
      </div>
      </form>
    @else
      <form method="POST" action="/calendars/{{$item['id']}}/status_update/rest">

@csrf
		<input type="text" name="dummy" style="display:none;" / >
        @method('PUT')
        @if(isset($student_id))
          <input type="hidden" value="{{$student_id}}" name="student_id" />
        @endif
        @if($user->role==="manager" || $user->role==="teacher")
        <input type="hidden" value="1" name="is_proxy">
        @endif

      <div class="row">
        @component('calendars.forms.rest_form', ['item' => $item, 'user'=>$user, 'student_id' => $student_id]) @endcomponent
        @component('calendars.forms.target_member', ['item' => $item, 'user'=>$user, 'status'=>'rest', 'student_id' => $student_id]) @endcomponent
      </div>

      <div class="row">
        <div class="col-12 col-md-6 mb-1">
            <button type="button" class="btn btn-submit btn-danger btn-block"  accesskey="{{$domain}}_action" confirm="休み連絡を送信しますか？">
              <i class="fa fa-envelope mr-1"></i>
              {{__('labels.rest_contact')}}
            </button>
        </div>
        <div class="col-12 col-md-6 mb-1">
            <button type="reset" class="btn btn-secondary btn-block">
                {{__('labels.close_button')}}

            </button>
        </div>

      </div>
      </form>
    @endif
  </div>
  @endslot
@endcomponent
