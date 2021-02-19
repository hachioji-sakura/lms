@section('title')
  {{__('labels.work_record')}}
@endsection
@extends('dashboard.common')
@section('page_sidemenu')
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
  <li class="nav-item has-treeview menu-open">
    <a href="#" class="nav-link">
    <i class="nav-icon fa fa-clock"></i>
    <p>
      {{__('labels.schedule_list')}}
      <i class="right fa fa-angle-left"></i>
    </p>
    </a>
    <ul class="nav nav-treeview pl-2">
      <li class="nav-item">
        <a class="nav-link" href="/events/{{$item->id}}/lesson_requests" >
          <i class="fa fa-envelope nav-icon"></i>
          申し込み一覧
        </a>
      </li>
      <li class="nav-item">
        <a href="/{{$domain}}/{{$item->id}}/schedule?list=history" class="nav-link @if($view=="schedule" && $list=="history") active @endif">
          <i class="fa fa-history nav-icon "></i>
          {{__('labels.schedule_history')}}
        </a>
      </li>
    </ul>
  </li>
  <li class="nav-item has-treeview menu-open">
    <a href="#" class="nav-link">
    <i class="nav-icon fa fa-business-time"></i>
    <p>
      マッチング予定
      <i class="right fa fa-angle-left"></i>
    </p>
    </a>
    <ul class="nav nav-treeview pl-2">
      <li class="nav-item ">
        <a href="/events/{{$item->id}}/schedules?search_status=fix" class="nav-link @if(empty($filter['calendar_filter']['search_status']) || $filter['calendar_filter']['search_status']=='fix') active @endif">
          <i class="fa fa-exclamation-triangle nav-icon"></i>
          予定仮確定
          <p>
          @if($fix_schedule_count>0)
            <span class="badge badge-primary right">{{$fix_schedule_count}}</span>
          @endif
          </p>
        </a>
      </li>
      <li class="nav-item ">
        <a href="/events/{{$item->id}}/schedules?search_status=complete" class="nav-link @if(isset($filter['calendar_filter']['search_status']) && $filter['calendar_filter']['search_status']=='complete') active @endif">
          <i class="fa fa-exclamation-triangle nav-icon"></i>
          予定確定済み
          <p>
          @if($complete_schedule_count>0)
            <span class="badge badge-primary right">{{$complete_schedule_count}}</span>
          @endif
          </p>
        </a>
      </li>
    </ul>
  </li>
</ul>
@endsection

@section('page_footer')
{{--
<dt>
  <a class="btn btn-app" href="javascript:void(0);" page_form="dialog" page_url="/comments/create?origin={{$domain}}&item_id={{$item->id}}" page_title="{{__('labels.comment_add')}}">
    <i class="fa fa-comment-dots"></i>{{__('labels.comment_add')}}
  </a>
</dt>
<dt>
  <a class="btn btn-app" href="javascript:void(0);" page_form="dialog" page_url="/calendars/create?teacher_id={{$item->id}}" page_title="{{__('labels.schedule_add')}}">
    <i class="fa fa-chalkboard-teacher"></i>{{__('labels.schedule_add')}}
  </a>
</dt>
--}}
@endsection


@section('contents')
<section class="content">
  <form id="lesson_request_calendars_post" method="POST"  action="/fuga">
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
              <h3 class="card-title text-center" id="charge_students">
                {{$item->title}}
              </h3>
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
            <ul id="lesson_request_calendars_list" class="mailbox-attachments clearfix row">
              @foreach($calendars as $calendar)
                @if($__date != $calendar["date"])
                <li class="col-12 p-0" accesskey="" target="">
                  <div class="row">
                    <div class="col-12 pl-3">
                      <a data-toggle="collapse" data-parent="#lesson_request_calendars_list" href="#{{date('Ymd', strtotime($calendar["date"]))}}" class="" aria-expanded="false">
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
                <div class="row pl-3 p-1 border-bottom  ">
                  <div class="col-12">
                    <span class="float-left">
                      <input class="form-check-input icheck flat-red day_check" type="checkbox" name="selected_lesson_request_calendar_ids[]" value="{{$calendar->id}}"  checked="checked"/>
                      <a href="javascript:void(0);" title="{{$calendar->id}}" page_title="{{__('labels.details')}}" page_form="dialog" page_url="/calendars/{{$calendar->id}}" role="button" class="">
                        {{$calendar->id}}
                      </a>
                      <span class="mr-2">
                        <small title="{{$calendar->id}}" class="badge badge-{{config('status_style')[$calendar->status]}} mt-1 mr-1">
                          {{$calendar->status_name}}
                        </small>
                      </span>
                      <span class="mr-2">
                        <i class="fa fa-clock"></i>{{$calendar->timezone}}
                      </span>
                      <span class="mr-2">
                        <i class="fa fa-map-marker"></i>{{$calendar->place_floor_name}}
                      </span>
                      <span class="mr-2">
                        <i class="fa fa-user-tie"></i>
                        {{$calendar->user->teacher->name()}}
                      </span>
                      <a href="/students/{{$calendar->student_id}} target="_blank" >
                      <span class="mr-2">
                        <i class="fa fa-user-graduate"></i>
                        {{$calendar->student_name}}
                      </span>
                      </a>
                      <span class="mr-2">
                        <small class="badge badge-success mt-1 mr-1">
                          <i class="fa fa-map-marker mr-1"></i>{{$calendar->place_floor_name}}
                        </small>
                      </span>
                      <span class="mr-2">
                        <small class="badge badge-primary mt-1 mr-1">
                          {{$calendar->subject}}
                        </small>
                      </span>
                      <br>
                    </span>
                    <span class="float-right">
                      <a href="javascript:void(0);" page_title="{{__('labels.schedule_edit')}}" page_form="dialog" page_url="/lesson_request_calendars/{{$calendar->id}}/edit" role="button" class="btn btn-default btn-sm ml-1">
                        <i class="fa fa-edit"></i>
                      </a>
                      <a href="javascript:void(0);" page_title="{{__('labels.schedule_delete')}}" page_form="dialog" page_url="/lesson_request_calendars/{{$calendar->id}}?action=delete" role="button" class="btn btn-default btn-sm ml-1">
                        <i class="fa fa-trash"></i>
                      </a>
                    </span>
                  </div>
                  <div class="col-12">
                    @if($calendar->is_charge_teacher()==false)
                    <span class="mr-2">
                      <small class="badge badge-danger mt-1 mr-1">
                        担当外講師
                      </small>
                    </span>
                    @endif
                    @if(!empty($calendar->matching_result))
                    <span class="mr-2">
                      <small class="badge badge-primary mt-1 mr-1">
                        {{$calendar->matching_result}}
                      </small>
                    </span>
                    @endif
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
          <div id="lesson_request_calendars_confirm" class="card-footer">
            <div class="row">
                <div class="col-12 mb-1">
                  <div class="form-group">
                    <label for="checked_at_type">
                      選択した仮確定予定を確定させますか？
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
                    <label for="remark" class="w-100">
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
            </div>
          </div>
        </div>
      </div>
    </div>
    <script>
    $(function(){
      base.pageSettinged('lesson_request_calendars_confirm', []);
      //submit
      $("button.btn-submit").on('click', function(e){
        console.log('submit');
        e.preventDefault();
        if(front.validateFormValue('lesson_request_calendars_confirm')){
          $(this).prop("disabled",true);
          $("#lesson_request_calendars_post").submit();
        }
      });
    });
    </script>
  </form>
</section>
@endsection
