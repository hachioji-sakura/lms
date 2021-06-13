@section('title')
{{$item->title}}
@endsection
@extends('dashboard.common')
@section('contents')
<section class="content">
  <form id="lesson_request_calendars_confirm"  method="POST" action='/events/{{$item->id}}/lesson_request_calendars/complete' >
    @csrf
    @method('PUT')
    <input type="text" name="dummy" style="display:none;" / >
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
              <h3 class="card-title text-center" id="charge_students">
                {{$item->title}}
                <br>
                <a href="#lesson_request_calendars_conmplete" class="btn btn-default btn-sm" scroll=true>
                  仮確定予定の確定
                </a>
              </h3>
          </div>
          <div class="card-body p-0">
            @if(count($calendars) > 0)
            <?php
              $__date = "";
            ?>
            <ul id="lesson_request_calendars_list" class="mailbox-attachments clearfix row">
              @foreach($calendars as $calendar)
                @if($calendar->teaching_type=='training') @continue @endif
                @if($__date != $calendar->date)
                <li class="col-12 p-0" accesskey="" target="">
                  <div class="row">
                    <div class="col-12 pl-3">
                      <a data-toggle="collapse" data-parent="#lesson_request_calendars_list" href="#{{date('Ymd', strtotime($calendar->date))}}" class="" aria-expanded="false">
                        <i class="fa fa-chevron-down mr-1"></i>
                        {{$calendar->dateweek}}
                      </a>
                    </div>
                  </div>
                  <div id="{{date('Ymd', strtotime($calendar->date))}}" class="collapse show">
                @endif
                <div class="row pl-3 p-1 border-bottom  ">
                  <div class="col-6">
                    <div class="row">
                      <div class="col-12 p-1">
                        <span class="float-left">
                          <input class="form-check-input icheck flat-red day_check" type="checkbox" name="selected_lesson_request_calendar_ids[]" value="{{$calendar->id}}"  checked="checked"/>
                          <a href="javascript:void(0);" title="{{$calendar->id}}" page_title="{{__('labels.details')}}" page_form="dialog" page_url="/lesson_request_calendars/{{$calendar->id}}" role="button" class="">
                            <span class="mr-2">
                              <small title="{{$calendar->id}}" class="badge badge-{{config('status_style')[$calendar->status]}} mt-1 mr-1">
                                {{$calendar->status_name}}
                              </small>
                            </span>
                            <span class="mr-2">
                              <i class="fa fa-clock"></i>{{$calendar->timezone}}
                            </span>
                          </a>
                          <span class="mr-2">
                            <a href="/students/{{$calendar->student->id}}" target="_blank" >
                              <i class="fa fa-user-graduate"></i>
                              {{$calendar->student->full_name}}
                            </a>
                          </span>
                          <span class="mr-2">
                            <small class="badge badge-success mt-1 mr-1">
                              <i class="fa fa-map-marker mr-1"></i>{{$calendar->place_floor_name}}
                            </small>
                          </span>
                        </span>
                      </div>
                      <div class="col-12 p-1">
                        <span class="float-left">
                          <span class="mr-2">
                            <a href="/teachers/{{$calendar->user->teacher->id}}" target = "_blank">
                              <i class="fa fa-user-tie"></i>
                              {{$calendar->user->teacher->name()}}
                            </a>
                          </span>
                          @if($calendar->is_charge_teacher()==false)
                          <span class="mr-2">
                            <small class="badge badge-danger mt-1 mr-1">
                              担当外講師
                            </small>
                          </span>
                          @endif
                          @if($calendar->is_teacher_place_enabled()==false)
                          <span class="mr-2">
                            <small class="badge badge-danger mt-1 mr-1">
                              講師の希望外校舎
                            </small>
                          </span>
                          @endif
                          <span class="mr-2">
                            <small class="badge badge-primary mt-1 mr-1">
                              {{$calendar->subject}}
                            </small>
                          </span>
                          @if(!empty($calendar->matching_result))
                          <span class="mr-2">
                            <small class="badge badge-primary mt-1 mr-1">
                              {{$calendar->matching_result}}
                            </small>
                          </span>
                          @endif
                        </span>
                        <a href="javascript:void(0);" page_title="{{__('labels.schedule_edit')}}" page_form="dialog" page_url="/lesson_request_calendars/{{$calendar->id}}/edit" role="button" class="btn btn-default btn-sm ml-1">
                          <i class="fa fa-edit"></i>
                        </a>
                        <a href="javascript:void(0);" page_title="{{__('labels.schedule_delete')}}" page_form="dialog" page_url="/lesson_request_calendars/{{$calendar->id}}?action=delete" role="button" class="btn btn-default btn-sm ml-1">
                          <i class="fa fa-trash"></i>
                        </a>
                      </div>
                    </div>
                  </div>
                  <div class="col-3">
                    <div class="row">
                    <span><i class="fa fa-dumbbell mr-1"></i>演習予定</span><br>
                    @foreach($calendar->training_calendars as $training_calendar)
                    <div class="col-12">
                      <span class="float-left">
                        <span class="mr-2">
                          <a href="javascript:void(0);" title="{{$training_calendar->id}}" page_title="{{__('labels.details')}}" page_form="dialog" page_url="/lesson_request_calendars/{{$training_calendar->id}}" >
                            <i class="fa fa-clock"></i>{{$training_calendar->timezone}}
                          </a>
                        </span>
                        <span class="mr-2">
                          <small title="{{$training_calendar->id}}" class="badge badge-{{config('status_style')[$training_calendar->teaching_type]}} mt-1 mr-1">
                            {{$training_calendar->teaching_type_name}}
                          </small>
                        </span>
                      </span>
                      <span class="float-left">
                        <a href="javascript:void(0);" page_title="{{__('labels.schedule_edit')}}" page_form="dialog" page_url="/lesson_request_calendars/{{$training_calendar->id}}/edit" role="button" class="btn btn-default btn-sm ml-1">
                          <i class="fa fa-edit"></i>
                        </a>
                        <a href="javascript:void(0);" page_title="{{__('labels.schedule_delete')}}" page_form="dialog" page_url="/lesson_request_calendars/{{$training_calendar->id}}?action=delete" role="button" class="btn btn-default btn-sm ml-1">
                          <i class="fa fa-trash"></i>
                        </a>
                      </span>
                    </div>
                    @endforeach
                    </div>
                  </div>
                  <div class="col-3">
                    @if(count($calendar->conflict_user_calendars())>0)
                      @if($calendar->lesson_request->has_tag('regular_schedule_exchange', 'true'))
                      <div class="row">
                        <span>
                          <small class="badge badge-warning mt-1 mr-1">
                            通常授業振替
                          </small>
                        </span><br>
                        @foreach($calendar->conflict_user_calendars() as $conflict_user_calendar)
                        <div class="col-12">
                          <span class="float-left">
                            <span class="mr-2">
                              <a href="javascript:void(0);" title="{{$conflict_user_calendar->id}}" page_title="{{__('labels.details')}}" page_form="dialog" page_url="/calendars/{{$conflict_user_calendar->id}}" >
                                <i class="fa fa-clock"></i>{{$conflict_user_calendar->timezone}}
                              </a>
                            </span>
                            <span class="mr-2">
                              <a href="/teachers/{{$conflict_user_calendar->user->teacher->id}}" target = "_blank">
                                <i class="fa fa-user-tie"></i>
                                {{$conflict_user_calendar->user->teacher->name()}}
                              </a>
                            </span>
                            <span class="mr-2">
                              <small title="{{$calendar->id}}" class="badge badge-{{config('status_style')[$conflict_user_calendar->status]}} mt-1 mr-1">
                                {{$conflict_user_calendar->teaching_type_name}}
                              </small>
                            </span>
                          </span>
                        </div>
                        @endforeach
                      </div>
                      @endif
                    @endif
                  </div>
                </div>
                <?php
                  $__date = $calendar->date;
                ?>
                @if($__date != $calendar->date)
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
          <div id="lesson_request_calendars_conmplete" class="card-footer">
            <div class="row">
                <div class="col-12 mb-1">
                  <div class="form-group">
                    <label for="checked_at_type">
                      選択した仮確定予定を確定させますか？
                      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
                    </label>
                    <div class="input-group" id="checked_at_type_form">
                      <div class="form-check">
                          <input class="form-check-input icheck flat-green" type="radio" name="checked_at_type" id="checked_at_type_fix" value="fix" required="true"  validate="select_id_check();">
                          <label class="form-check-label" for="checked_at_type_fix">
                              {{__('labels.yes')}}
                          </label>
                      </div>
                      <div class="form-check ml-2">
                          <input class="form-check-input icheck flat-green" type="radio" name="checked_at_type" id="checked_at_type_cancel" value="cancel" required="true" >
                          <label class="form-check-label" for="checked_at_type_cancel">
                              {{__('labels.no')}}
                          </label>
                      </div>
                    </div>
                  </div>
                </div>
                <script>
                function select_id_check(){
                  var _is_scceuss = false;
                  $("input[name='selected_lesson_request_calendar_ids[]']:checked").each(function(index, value){
                    var val = $(this).val();
                    if(val!=1){
                      _is_scceuss = true;
                    }
                  });
                  if(!_is_scceuss){
                    front.showValidateError('#checked_at_type_form', '更新対象をつ以上選択してください');
                  }
                  return _is_scceuss;
                }
                </script>
                <div class="col-12 mb-1">
                  <button type="button" class="btn btn-submit btn-info btn-block">
                    <i class="fa fa-check mr-1"></i>
                    {{__('labels.update_button')}}
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
          $("#lesson_request_calendars_confirm").submit();
        }
      });
    });
    </script>
  </form>
</section>
@endsection
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
          <i class="fa fa-calendar-check nav-icon"></i>
          予定確定済み
          <p>
          @if($complete_schedule_count>0)
            <span class="badge badge-success right">{{$complete_schedule_count}}</span>
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
