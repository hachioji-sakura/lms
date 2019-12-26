@section('title')
  {{__('labels.work_record')}}
@endsection
@extends('dashboard.common')
@include($domain.'.menu')


@section('contents')
<section class="content">
  <form method="POST"  action="/{{$domain}}/{{$item->id}}/month_work">
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    <input type="hidden" name="target_month" value="{{$target_month}}" >
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <div class="row">
              <div class="col-3">
                <ul class="pagination pagination-sm m-0">
                  <li class="page-item">
                    <a class="page-link" href="{{sprintf('/%s/%d/month_work/%s', $domain, $item->id, $prev_month)}}">
                      <i class="fa fa-chevron-left mx-1"></i>
                      <span class="d-none d-sm-inline-block">
                        {{__('labels.year_month', ['year'=>date('Y',strtotime($prev_month.'-01')), 'month' => date('m',strtotime($prev_month.'-01'))])}}
                      </span>
                    </a>
                  </li>
                </ul>
              </div>
              <div class="col-6 text-center">
                <h3 class="card-title" id="charge_students">
                  {{__('labels.year_month', ['year'=>$year, 'month' => $month])}}
                  {{__('labels.work_record')}}
                  @if(date('Y-m')==$target_month)
                  <a href="#{{date("Ymd")}}" class="btn btn-default btn-sm mx-2" scroll=true>
                    {{__('labels.calendar_button_today')}}
                  </a>
                  @endif
                </h3>
              </div>
              <div class="col-3">
                <ul class="pagination pagination-sm m-0 float-right">
                  <li class="page-item">
                    <a class="page-link" href="{{sprintf('/%s/%d/month_work/%s', $domain, $item->id, $next_month)}}">
                      <span class="d-none d-sm-inline-block">
                        {{__('labels.year_month', ['year'=>date('Y',strtotime($next_month.'-01')), 'month' => date('m',strtotime($next_month.'-01'))])}}
                      </span>
                      <i class="fa fa-chevron-right"></i>
                    </a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <div class="card-body p-0">
            @if(count($calendars) > 0)
            <?php
              $__date = "";
            ?>
            @if($is_checked==true)
                <h6 class="text-sm p-1 pl-2 mt-2 bg-secondary" >
                  <i class="fa fa-info-circle mr-1"></i>
                  {!!nl2br(__('messages.info_work_record_all_check'))!!}
                </h6>
            @elseif($enable_confirm==false)
                <h6 class="text-sm p-1 pl-2 mt-2 bg-danger" >
                  <i class="fa fa-exclamation-triangle mr-1"></i>
                  @if($user->user_id === $item->user_id)
                  {!!nl2br(__('messages.error_work_record'))!!}
                  @else
                  {!!nl2br(__('messages.error_work_record_not_owner'))!!}
                  @endif
                </h6>
            @endif
            <ul id="month_work_list" class="mailbox-attachments clearfix row">
              @foreach($calendars as $calendar)
              <?php $calendar = $calendar->details(1); ?>
                @if($__date != $calendar["date"])
                <li class="col-12 p-0" accesskey="" target="">
                  <div class="row">
                    <div class="col-12 pl-3">
                      <a data-toggle="collapse" data-parent="#month_work_list" href="#{{date('Ymd', strtotime($calendar["date"]))}}" class="" aria-expanded="false">
                        <i class="fa fa-chevron-down mr-1"></i>
                        {{$calendar["dateweek"]}}

                        @if(date('Y-m-d')==date('Y-m-d', strtotime($calendar["date"])))
                          <small class="badge badge-danger ml-1">
                            {{__('labels.calendar_button_today')}}
                          </small>
                        @endif
                      </a>
                    </div>
                  </div>
                  <div id="{{date('Ymd', strtotime($calendar["date"]))}}" class="collapse show">
                @endif
                <div class="row pl-3 p-1 border-bottom calendar_{{$calendar['status']}}">
                  <input type="hidden" name="calendar_id[]" value="{{$calendar['id']}}" >
                  <div class="col-12 col-lg-3 col-md-3">
                    <a href="javascript:void(0);" title="{{$calendar["id"]}}" page_title="{{__('labels.details')}}" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}" role="button" class="">
                      <span class="mr-2">
                        <i class="fa fa-clock"></i>{{$calendar["timezone"]}}
                      </span>
                      <span class="mr-2">
                        <i class="fa fa-map-marker"></i>{{$calendar["place_floor_name"]}}
                      </span>
                      <span class="text-xs mr-2">
                        <small class="badge badge-{{config('status_style')[$calendar->status]}} mt-1 mr-1">
                          {{$calendar["status_name"]}}
                        </small>
                      </span>
                    </a>
                  </div>
                  <div class="col-12 col-lg-5 col-md-5">
                    @foreach($calendar->members as $member)
                      @if($member->user->details()->role==="student")
                        @if($member->status=="rest")
                        <span class="mr-2 text-danger">
                        <i class="fa fa-user-times"></i>
                        {{$member->user->details()->name}}
                        </span>
                        @else
                        <span class="mr-2">
                        <i class="fa fa-user-graduate"></i>
                        {{$member->user->details()->name}}
                        </span>
                        @endif
                      @endif
                    @endforeach
                    @if($calendar->is_management()==false)
                      @foreach($calendar['subject'] as $subject)
                      <span class="text-xs mx-2">
                        <small class="badge badge-primary mt-1 mr-1">
                          {{$subject}}
                        </small>
                      </span>
                      @endforeach
                    @else
                    <span class="text-xs mx-2">
                      <small class="badge badge-primary mt-1 mr-1">
                        {{$calendar["work_name"]}}
                      </small>
                    </span>
                    @endif
                  </div>
                  <div class="col-12 col-lg-4 col-md-4">
                    @component('teachers.forms.calendar_button', ['teacher'=>$item, 'calendar' => $calendar, 'user'=>$user, 'domain'=>$domain, 'domain_name'=>$domain_name])
                    @endcomponent
                  </div>
                </div>
                <?php
                  $__date = $calendar["date"];
                ?>
                @if($__date != $calendar["date"])
                  </div>
                </li>
                @endif
              @endforeach
            </ul>
            @else
            <div class="alert">
              <h4><i class="icon fa fa-exclamation-triangle"></i>{{__('labels.no_data')}}</h4>
            </div>
            @endif
          </div>
          @if(count($calendars) > 0)
          <div id="month_work_confirm" class="card-footer">
            <div class="row">
              @if($is_checked==false && $enable_confirm===true)
                <div class="col-12 mb-1">
                  <div class="form-group">
                    <label for="checked_at_type">
                      この勤怠の内容でお間違いないでしょうか？
                      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
                    </label>
                    <div class="input-group">
                      <div class="form-check">
                          <input class="form-check-input icheck flat-green" type="radio" name="checked_at_type" id="checked_at_type_fix" value="fix" required="true" onChange="checked_at_type_radio_change()">
                          <label class="form-check-label" for="checked_at_type_fix">
                              {{__('labels.yes')}}
                          </label>
                      </div>
                      <div class="form-check ml-2">
                          <input class="form-check-input icheck flat-green" type="radio" name="checked_at_type" id="checked_at_type_cancel" value="cancel" required="true"  onChange="checked_at_type_radio_change()">
                          <label class="form-check-label" for="checked_at_type_cancel">
                              {{__('labels.no')}}
                          </label>
                      </div>
                    </div>
                  </div>
                </div>
                <script>
                function checked_at_type_radio_change(obj){
                  var is_cancel = $('input[type="radio"][name="checked_at_type"][value="cancel"]').prop("checked");
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
                      訂正内容をご連絡ください
                      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
                    </label>
                    <textarea type="text" name="remark" class="form-control" placeholder="例：X月X日 15時～16時の出席を欠席に変更したい。" required="true"></textarea>
                  </div>
                </div>
                <div class="col-12 mb-1">
                  <button type="button" class="btn btn-submit btn-info btn-block">
                    <i class="fa fa-envelope mr-1"></i>
                    {{__('labels.send_button')}}
                  </button>
                </div>
              @endif
            </div>
          </div>
          @endif
        </div>
      </div>
    </div>
    <script>
    $(function(){
      base.pageSettinged('month_work_confirm', []);
      //submit
      $("button.btn-submit").on('click', function(e){
        e.preventDefault();
        if(front.validateFormValue('month_work_confirm')){
          $("form").submit();
        }
      });
    });
    </script>
  </form>
</section>
@endsection
