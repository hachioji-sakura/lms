@section('title')
  {{$domain_name}}詳細
@endsection
@extends('dashboard.common')
@include($domain.'.menu')

@section('contents')
<section id="member" class="content-header">
	<div class="container-fluid">
		<div class="row">
      <div class="col-5">
        <!-- Widget: user widget style 1 -->
        <div class="card card-widget widget-user">
          <!-- Add the bg color to the header using any of the bg-* classes -->
          <div class="widget-user-header bg-info-active">
            <h3 class="widget-user-username">
              {{$item->student->name()}}
              <span class="text-sm mx-2">
                <small class="badge badge-secondary mt-1 mr-1">
                  {{$item->student->gender()}}
                </small>
              </span>
              <span class="text-sm mx-2">
                <small class="badge badge-secondary mt-1 mr-1">
                  {{$item->student->grade()}}
                </small>
              </span>
            </h3>
            <h5 class="widget-user-desc">{{$item->student->school_name()}}</h5>
          </div>
          <div class="widget-user-image">
            <img class="img-circle elevation-2" src="{{$item->student->icon()}}" alt="User Avatar">
          </div>
          <div class="card-footer">
            <div class="row">
              <div class="col-sm-6 border-right">
                <div class="description-block">
                  <h5 class="description-header">対応状況</h5>
                  <span class="description-text">
                    <small class="badge badge-{{$item["status_style"]}} mx-2">
                      {{$item["status_name"]}}
                    </small>
                  </span>
                </div>
              </div>

              <div class="col-sm-6 border-right">
                <div class="description-block">
                  <h5 class="description-header">ご希望のレッスン</h5>
                  <span class="description-text">
                    @foreach($item["tagdata"]['lesson'] as $label)
                    <span class="text-xs mx-2">
                      <small class="badge badge-info mt-1 mr-1">
                        {{$label}}
                      </small>
                    </span>
                    @endforeach
                    <span class="text-xs mx-2">
                      <small class="badge badge-info mt-1 mr-1">
                        週{{$item->student->tag_name("lesson_week_count")}}回
                      </small>
                    </span>
                    <span class="text-xs mx-2">
                      <small class="badge badge-info mt-1 mr-1">
                        {{$item->student->tag_name("course_minutes")}}授業
                      </small>
                    </span>
                  </span>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="description-block">
                  <h5 class="description-header">希望日時</h5>
                  <span class="description-text">
                    第１希望：<span class="text-xs mx-2">
                      <small class="badge badge-secondary ">
                        {{$item["date1"]}}
                      </small><br>
                    第２希望：<span class="text-xs mx-2">
                      <small class="badge badge-secondary ">
                        {{$item["date2"]}}
                      </small>
                    </span>
                    </span>
                  </span>
                </div>
              </div>

              <div class="col-sm-6">
                <div class="description-block">
                  <h5 class="description-header">ご希望の教室</h5>
                  <span class="description-text">
                    @foreach($item["tagdata"]['lesson_place'] as $label)
                      <span class="text-xs mx-2">
                        <small class="badge badge-success mt-1 mr-1">
                          {{$label}}
                        </small>
                      </span>
                    @endforeach
                  </span>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
      <div class="col-7">
        <!-- Widget: user widget style 1 -->
        <div class="card card-widget">
          <div class="card-header">
            <i class="fa fa-envelope-open-text mr-1"></i>申込内容
            <span class="text-muted text-sm mx-2 float-right">
              <i class="fa fa-clock mr-1"></i>{{$item["created_at"]}}
            </span>
          </div>
          <div class="card-footer">
            <div class="row">
              <div class="col-sm-6 border-right">
                <div class="description-block">
                  <h5 class="description-header">希望科目（補習）</h5>
                  <span class="description-text">
                    @if(count($item["subject2"])>0)
                      @foreach($item["subject2"] as $label)
                          <span class="text-xs mx-2">
                            <small class="badge badge-primary mt-1 mr-1">
                              {{$label}}
                            </small>
                          </span>
                      @endforeach
                    @else
                      なし
                    @endif
                  </span>
                </div>
              </div>
              <div class="col-sm-6 border-right">
                <div class="description-block">
                  <h5 class="description-header">希望科目（受験対策）</h5>
                  <span class="description-text">
                    @if(count($item["subject1"])>0)
                      @foreach($item["subject1"] as $label)
                          <span class="text-xs mx-2">
                            <small class="badge badge-primary mt-1 mr-1">
                              {{$label}}
                            </small>
                          </span>
                      @endforeach
                    @else
                    <div class="nav-link w-100 ml-4">
                      なし
                    </div>
                    @endif
                  </span>
                </div>
              </div>
              @isset($item["tagdata"]['english_teacher'])
              <div class="col-sm-6 border-right">
                <div class="description-block">
                  <h5 class="description-header">英会話希望講師</h5>
                  <span class="description-text">
                    @foreach($item["tagdata"]['english_teacher'] as $label)
                    <span class="text-xs mx-2">
                      <small class="badge badge-secondary mt-1 mr-1">
                        {{$label}}
                      </small>
                    </span>
                    @endforeach
                  </span>
                </div>
              </div>
              @endisset
              @isset($item["tagdata"]['course_type'])
              <div class="col-sm-6 border-right">
                <div class="description-block">
                  <h5 class="description-header">授業形式</h5>
                  <span class="description-text">
                    @foreach($item["tagdata"]['course_type'] as $label)
                    <span class="text-xs mx-2">
                      <small class="badge badge-secondary mt-1 mr-1">
                        {{$label}}
                      </small>
                    </span>
                    @endforeach
                  </span>
                </div>
              </div>
              @endisset
              @isset($item["tagdata"]['piano_level'])
              <div class="col-sm-6 border-right">
                <div class="description-block">
                  <h5 class="description-header">ピアノのご経験</h5>
                  <span class="description-text">
                    @foreach($item["tagdata"]['piano_level'] as $label)
                    <span class="text-xs mx-2">
                      <small class="badge badge-secondary mt-1 mr-1">
                        {{$label}}
                      </small>
                    </span>
                    @endforeach
                  </span>
                </div>
              </div>
              @endisset
              @isset($item["tagdata"]['kids_lesson'])
              <div class="col-sm-6 border-right">
                <div class="description-block">
                  <h5 class="description-header">ご希望の習い事</h5>
                  <span class="description-text">
                    @foreach($item["tagdata"]['kids_lesson'] as $label)
                    <span class="text-xs mx-2">
                      <small class="badge badge-secondary mt-1 mr-1">
                        {{$label}}
                      </small>
                    </span>
                    @endforeach
                  </span>
                </div>
              </div>
              @endisset
              <div class="col-sm-12">
                <div class="description-block">
                  <h5 class="description-header">ご要望</h5>
                  <span class="description-text">
                    {{$item["remark"]}}
                  </span>
                </div>
              </div>
            </div>
            <!-- /.row -->
          </div>
        </div>
        <!-- /.widget-user -->
      </div>
		</div>
	</div>
</section>
<section class="content-header">
	<div class="container-fluid">
		<div class="row">
      <div class="col-5">
        <!-- Widget: user widget style 1 -->
        <div class="card card-widget">
          <div class="card-header">
            <i class="fa fa-calendar mr-1"></i>希望スケジュール
          </div>
          <div class="card-footer">
            <div class="row">
              <div class="col-12">
                <div class="form-group">
                  <table class="table table-striped">
                  <tr class="bg-gray">
                    <th class="p-1 text-center border-right">時間帯 / 曜日</th>
                    @foreach($attributes['lesson_week'] as $index => $name)
                    <th class="p-1 text-center border-right lesson_week_label
                    @if($index==="sat") text-primary
                    @elseif($index==="sun") text-danger
                    @endif
                    " alt="{{$index}}">
                       {{$name}}
                    </th>
                    @endforeach
                  </tr>
                  @foreach($attributes['lesson_time'] as $index => $name)
                  <tr class="">
                    <th class="p-1 text-center bg-gray text-sm lesson_week_time_label">{{$name}}</th>
                    @foreach($attributes['lesson_week'] as $week_code => $week_name)
                    <td class="p-1 text-center border-right" id="lesson_{{$week_code}}_time_{{$index}}_name">
                      @if(isset($item) && isset($item->student->user) && $item->student->user->has_tag('lesson_'.$week_code.'_time', $index)===true)
                        〇
                      @else
                        {{$item->student->user->has_tag('lesson_'.$week_code.'_time', $index)}}
                      @endif
                    </td>
                    @endforeach
                  </tr>
                  @endforeach
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-7">
        <div class="card card-widget">
          <div class="card-header">
            <i class="fa fa-envelope-open-text mr-1"></i>体験授業予定
            <a role="button" class="btn btn-flat btn-info float-right" href="/trials/{{$item["id"]}}/to_calendar">
              <i class="fa fa-plus mr-1"></i>体験授業予定を設定する
            </a>
          </div>
          <div class="card-footer">
        @if(count($item["calendars"])>0)
          @foreach($item["calendars"] as $calendar)
              <div class="row">
                <div class="col-sm-3 border-right">
                  <div class="description-block">
                    <h5 class="description-header">対応状況</h5>
                    <span class="description-text">
                      <small class="badge badge-{{$calendar->status_style()}} mx-2">
                        {{$calendar["status_name"]}}
                      </small>
                    </span>
                  </div>
                </div>
                <div class="col-sm-4 border-right">
                  <div class="description-block">
                    <h5 class="description-header">予定</h5>
                    <span class="description-text">
                      <i class="fa fa-clock mr-1"></i>
                      {{$calendar["datetime"]}}
                    </span>
                  </div>
                </div>
                <div class="col-sm-2 border-right">
                  <div class="description-block">
                    <h5 class="description-header">講師</h5>
                    <span class="description-text">
                      <i class="fa fa-user-tie mr-1"></i>
                      {{$calendar['teacher_name']}}
                    </span>
                  </div>
                </div>
                <div class="col-sm-3 ">
                  <div class="description-block">
                    <h5 class="description-header">内容</h5>
                    <span class="description-text">
                        <span class="text-xs mx-2">
                          <small class="badge badge-primary mt-1 mr-1">
                            {{$calendar["lesson"]}}
                            {{$calendar["course"]}}
                            {{$calendar["subject"]}}
                          </small>
                        </span>
                    </span>
                  </div>
                </div>
              </div>
          @endforeach
        @else
          <i class="fa fa-exclamation-triangle mr-1"></i>
          授業予定が登録されていません
        @endif
          </div>
        </div>
      </div>
		</div>

		</div>
	</div>
</section>

@endsection
