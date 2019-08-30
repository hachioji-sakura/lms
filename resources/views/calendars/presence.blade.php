@component('calendars.page', ['item' => $item, 'fields' => $fields, 'domain' => $domain, 'action' => $action, 'user'=>$user])
  @slot('page_message')
    @if(config('app.env')!='product' || strtotime($item->start_time) <= strtotime('15 minute') || strtotime($item->end_time) <= strtotime('1 minute'))
      {!!nl2br(__('messages.warning_calendar_presence'))!!}
    @else
      <div class="col-12 col-lg-12 col-md-12 mb-1">
        <h4 class="text-danger">{!!nl2br(__('messages.warning_calendar_presence_time'))!!}</h4>
      </div>
    @endif
  @endslot
  @slot('forms')
  <div id="{{$domain}}_presence">
    @if(count($item["students"]) > 1)
      {{-- グループレッスン系 --}}
      <form method="POST" action="/calendars/{{$item['id']}}">
        @csrf
        <input type="text" name="dummy" style="display:none;" / >
        @method('PUT')
        <div class="row border-top">
          <div class="col-12 mb-1">
          <div class="form-group">
            <label for="status">
              <i class="fa fa-question-circle mr-1 mt-2"></i>
              {!!nl2br(__('messages.confirm_lecture_exec'))!!}
              <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
            </label>
            <div class="input-group">
              <div class="form-check">
                <input class="form-check-input icheck flat-green" type="radio" name="status" id="status_presence" value="presence" required="true" onChange="status_change();" validate="status_presence_check();">
                <label class="form-check-label" for="status_presence">
                    {!!nl2br(__('messages.lecture_exec'))!!}
                </label>
              </div>
              <div class="form-check ml-2">
                <input class="form-check-input icheck flat-red" type="radio" name="status" id="status_absence" value="absence" required="true" onChange="status_change();" validate="status_presence_check();">
                <label class="form-check-label" for="status_absence">
                    {!!nl2br(__('messages.not_lecture_exec'))!!}
                </label>
              </div>
            </div>
          </div>
        </div>
        </div>
        <script>
        function status_change(){
          var status = $("input[name='status']:checked").val();
          if(status=="presence"){
            $("#presence_list").collapse('show');
            $("#presence_list input").show();
          }
          else {
            $("#presence_list").collapse('hide');
            $("#presence_list input").hide();
          }
        }
        function status_presence_check(){
          console.log("status_presence_check");
          var _is_scceuss = false;
          var status = $("input[name='status']:checked").val();
          if(status=="presence"){
            //実施
            $("input.presence_check[type='radio']:checked").each(function(index, value){
              var val = $(this).val();
              console.log(val);
              if(val=="presence"){
                //一人でも出席がいる
                _is_scceuss = true;
              }
            });
            if(!_is_scceuss){
              front.showValidateError('#presence_list_table', '{!!nl2br(__('messages.error_calendar_presence_not_student'))!!}');
            }
          }
          else {
            _is_scceuss = true;
            //実施していない
            $("input.presence_check[type='radio']:checked").each(function(index, value){
              var val = $(this).val();
              console.log(val);
              if(val=="presence"){
                //一人でも出席がいる
                _is_scceuss = false;
              }
            });
            if(!_is_scceuss){
              front.showValidateError('#presence_list_table', '{!!nl2br(__('messages.error_calendar_presence_not_exec'))!!}');
            }
          }
          return _is_scceuss;
        }
        </script>
        <div class="row collapse" id="presence_list">
          <div class="col-12 mb-1">
            <div class="form-group">
              <label for="status">
                <i class="fa fa-question-circle mr-1"></i>
                {!!nl2br(__('messages.confirm_student_presence'))!!}
                <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
              </label>
            </div>
          </div>
          <div class="col-12">
            <table class="table table-striped w-80" id="presence_list_table">
              <tr class="bg-gray">
                <th class="p-1 pl-2 text-sm "><i class="fa fa-user mr-1"></i>{{__('labels.students')}}</th>
                <th class="p-1 pl-2 text-sm"><i class="fa fa-check mr-1"></i>{{__('labels.calendar_button_attendance')}}</th>
              </tr>
              @foreach($item["students"] as $member)
              @if($member->user->details()->role==="student")
              <tr class="">
                <th class="p-1 pl-2">
                  {{$member->user->details()->name}}</th>
                <td class="p-1 text-sm text-center">
                  @if($member->status!="fix" )
                    <i class="fa fa-times mr-1"></i>{{$member->status_name()}}
                  @else
                  <div class="input-group">
                    <div class="form-check">
                      <input class="form-check-input icheck flat-green presence_check" type="radio" name="{{$member->id}}_status" id="{{$member->id}}_status_presence" value="presence" required="true" >
                      <label class="form-check-label" for="{{$member->id}}_status_presence">
                        @if($item->work==9)
                        {{__('labels.calendar_button_working')}}
                        @else
                        {{__('labels.calendar_button_presence')}}
                        @endif
                      </label>
                    </div>
                    <div class="form-check ml-2">
                      <input class="form-check-input icheck flat-red presence_check" type="radio" name="{{$member->id}}_status" id="{{$member->id}}_status_absence" value="absence" required="true" >
                      <label class="form-check-label" for="{{$member->id}}_status_absence">
                        @if($item->work==9)
                        {{__('labels.calendar_button_no_working')}}
                        @else
                        {{__('labels.calendar_button_absence')}}
                        @endif
                      </label>
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
        @if(config('app.env')!='product' || strtotime($item->start_time) <= strtotime('15 minute') || strtotime($item->end_time) <= strtotime('1 minute'))
          {{-- 当日開始15分前～終了15分後までの表示 --}}
          <div class="row">
            <div class="col-12 col-lg-6 col-md-6 mb-1">
              <button type="button" class="btn btn-submit btn-success btn-block"  accesskey="{{$domain}}_presence" {{__('labels.close_button')}}
                  confirm="{{__('messages.confirm_update')}}">
                  <i class="fa fa-check-circle mr-1"></i>
                  {{__('labels.schedule_presence')}}
              </button>
            </div>
            <div class="col-12 col-lg-6 col-md-6 mb-1" id="{{$domain}}_presence">
              <button type="reset" class="btn btn-secondary btn-block">
                  {{__('labels.close_button')}}
              </button>
            </div>
          </div>
        @else
          <div class="row">
            <div class="col-12 col-lg-12 col-md-12 mb-1">
                <button type="reset" class="btn btn-secondary btn-block">
                    {{__('labels.close_button')}}
                </button>
            </div>
          </div>
        @endif
      </form>
    @else
      {{-- マンツーマン系 --}}
      <div class="row">
        <div class="col-12 col-lg-6 col-md-6 mb-1" id="{{$domain}}_single_presence">
          <form method="POST" action="/calendars/{{$item['id']}}/status_update/presence">
            @csrf
		        <input type="text" name="dummy" style="display:none;" / >
            <input type="hidden" value="1" name="is_all_student" />
            @method('PUT')
            <button type="button" class="btn btn-success btn-submit btn-block"  accesskey="{{$domain}}_single_presence" {{__('labels.close_button')}}
confirm="{{__('messages.confirm_update')}}">
                <i class="fa fa-check-circle mr-1"></i>
                @if($item->work==9)
                {{__('labels.calendar_button_working')}}
                @else
                {{__('labels.calendar_button_presence')}}
                @endif
            </button>
          </form>
        </div>
        <div class="col-12 col-lg-6 col-md-6 mb-1" id="{{$domain}}_single_absence">
          <form method="POST" action="/calendars/{{$item['id']}}/status_update/absence">
            @csrf
		        <input type="text" name="dummy" style="display:none;" / >
            <input type="hidden" value="1" name="is_all_student" />
            @method('PUT')
            <button type="button" class="btn btn-danger btn-submit btn-block"  accesskey="{{$domain}}_single_absence" confirm="{{__('messages.confirm_update')}}">
              <i class="fa fa-times-circle mr-1"></i>
              @if($item->work==9)
              {{__('labels.calendar_button_no_working')}}
              @else
              {{__('labels.calendar_button_absence')}}
              @endif
            </button>
          </form>
        </div>
        <div class="col-12 col-lg-12 col-md-12 mb-1">
            <button type="reset" class="btn btn-secondary btn-block">
                {{__('labels.close_button')}}

            </button>
        </div>
      </div>
    @endif
  </div>
  @endslot
@endcomponent
