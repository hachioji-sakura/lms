@section('title')
  {{$domain_name}}月次勤務実績
@endsection
@extends('dashboard.common')
@include($domain.'.menu')


@section('contents')
<section class="content">
  <form method="POST"  action="/{{$domain}}/{{$item->id}}/month_work">
  @csrf
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
                    <span class="d-none d-sm-inline-block">{{date('Y年m月',strtotime($prev_month.'-01'))}}</span>
                  </a>
                </li>
              </ul>
            </div>
            <div class="col-6 text-center">
              <h3 class="card-title" id="charge_students">
                {{$list_title}}
              </h3>
            </div>
            <div class="col-3">
              <ul class="pagination pagination-sm m-0 float-right">
                <li class="page-item">
                  <a class="page-link" href="{{sprintf('/%s/%d/month_work/%s', $domain, $item->id, $next_month)}}">
                    <span class="d-none d-sm-inline-block">{{date('Y年m月',strtotime($next_month.'-01'))}}</span>
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
                この勤怠はすべて確認済みです
              </h6>
          @elseif($enable_confirm==false)
              <h6 class="text-sm p-1 pl-2 mt-2 bg-danger" >
                <i class="fa fa-exclamation-triangle mr-1"></i>
                @if($user->user_id === $item->user_id)
                授業予定がまだ残っているため、月次勤怠の確定はできません
                @else
                勤務実績確認は、本人のみ可能です
                @endif
              </h6>
          @endif
          <ul id="month_work_list" class="mailbox-attachments clearfix row">
            @foreach($calendars as $calendar)
              @if($__date != $calendar["date"])
              <li class="col-12 p-0" accesskey="" target="">
                <div class="row">
                  <div class="col-12 pl-3">
                    <a data-toggle="collapse" data-parent="#month_work_list" href="#{{date('Ymd', strtotime($calendar["date"]))}}" class="" aria-expanded="false">
                      <i class="fa fa-chevron-down mr-1"></i>
                      {{date('m月d日', strtotime($calendar["date"]))}}
                    </a>
                  </div>
                </div>
                <div id="{{date('Ymd', strtotime($calendar["date"]))}}" class="collapse show">
              @endif
              <div class="row pl-3 p-1 border-bottom">
                <input type="hidden" name="calendar_id[]" value="{{$calendar['id']}}" >
                <div class="col-12 col-lg-4 col-md-4">
                  <a href="javascript:void(0);" title="{{$calendar["id"]}}" page_title="詳細" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}" role="button" class="">
                    <span class="mr-2">
                      <i class="fa fa-clock"></i>{{$calendar["timezone"]}}
                    </span>
                    <span class="mr-2">
                      <i class="fa fa-map-marker"></i>{{$calendar->place()}}
                    </span>
                    <span class="text-xs mr-2">
                      <small class="badge badge-{{config('status_style')[$calendar->status]}} mt-1 mr-1">
                        {{$calendar["status_name"]}}
                      </small>
                    </span>
                  </a>
                </div>
                <div class="col-12 col-lg-6 col-md-6">
                  @foreach($calendar->members as $member)
                    @if($member->user->details()->role==="student")
                    {{--
                      <a href="/students/{{$member->user->details()->id}}">
                        <i class="fa fa-user-graduate"></i>
                        {{$member->user->details()->name}}
                      </a>
                      --}}
                      <span class="mr-2">
                      <i class="fa fa-user-graduate"></i>
                      {{$member->user->details()->name}}
                      </span>
                    @endif
                  @endforeach
                  @foreach($calendar['subject'] as $subject)
                  <span class="text-xs mr-2">
                    <small class="badge badge-primary mt-1 mr-1">
                      {{$subject}}
                    </small>
                  </span>
                  @endforeach
                </div>
                <div class="col-12 col-lg-2 col-md-2">
                  <a href="javascript:void(0);" title="{{$calendar["id"]}}" page_title="詳細" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}" role="button" class="btn btn-default btn-sm float-left mr-1 mt-1 float-right">
                    <i class="fa fa-edit"></i>変更
                  </a>
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
            <h4><i class="icon fa fa-exclamation-triangle"></i>データがありません</h4>
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
                    <span class="right badge badge-danger ml-1">必須</span>
                  </label>
                  <div class="input-group">
                    <div class="form-check">
                        <input class="form-check-input icheck flat-green" type="radio" name="checked_at_type" id="checked_at_type_fix" value="fix" required="true" onChange="checked_at_type_radio_change()">
                        <label class="form-check-label" for="checked_at_type_fix">
                            はい
                        </label>
                    </div>
                    <div class="form-check ml-2">
                        <input class="form-check-input icheck flat-green" type="radio" name="checked_at_type" id="checked_at_type_cancel" value="cancel" required="true"  onChange="checked_at_type_radio_change()">
                        <label class="form-check-label" for="checked_at_type_cancel">
                            いいえ
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
                    <span class="right badge badge-danger ml-1">必須</span>
                  </label>
                  <textarea type="text" name="remark" class="form-control" placeholder="例：X月X日 15時～16時の出席を欠席に変更したい。" required="true"></textarea>
                </div>
              </div>
              <div class="col-12 mb-1">
                <button type="button" class="btn btn-submit btn-info btn-block">
                  <i class="fa fa-envelope mr-1"></i>
                  送信
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
