@section('title')
  {{$domain_name}}詳細
@endsection
@extends('dashboard.common')
@include($domain.'.menu')

@section('contents')
<section id="member" class="content-header">
	<div class="container-fluid">
		<div class="row">
      <div class="col-12">
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
              <div class="col-sm-4 border-right">
                <div class="description-block">
                  <h5 class="description-header">ご希望レッスン</h5>
                  <span class="description-text">{{$item["lesson"]}}</span>
                </div>
              </div>
              <div class="col-sm-4 border-right">
                <div class="description-block">
                  <h5 class="description-header">ご希望の場所</h5>
                  <span class="description-text">{{$item["lesson_place"]}}</span>
                </div>
              </div>
              <div class="col-sm-4">
                <div class="description-block">
                  <h5 class="description-header">希望授業時間</h5>
                  <span class="description-text">{{$item["course_minutes"]}}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
		</div>
	</div>
</section>
<section class="content-header">
	<div class="container-fluid">
		<div class="row">
      <div class="col-12">
        <!-- Widget: user widget style 1 -->
        <div class="card card-widget">
          <div class="card-header">
            <i class="fa fa-envelope mr-1"></i>体験申込内容
            <small class="badge badge-{{$item["status_style"]}} mx-2">
              {{$item["status_name"]}}
            </small>
            <span class="text-muted text-sm mx-2 float-right">
              <i class="fa fa-clock mr-1"></i>{{$item["created_at"]}}
            </span>
          </div>
          <div class="card-footer">
            <div class="row">
              <div class="col-sm-6 border-right">
                <div class="description-block">
                  <h5 class="description-header">希望日時１</h5>
                  <span class="description-text">{{$item["date1"]}}</span>
                </div>
              </div>
              <div class="col-sm-6 border-right">
                <div class="description-block">
                  <h5 class="description-header">希望日時２</h5>
                  <span class="description-text">{{$item["date2"]}}</span>
                </div>
              </div>
              <div class="col-sm-6 border-right">
                <div class="description-block">
                  <h5 class="description-header">希望科目（補習）</h5>
                  <span class="description-text">{{$item["subject1"]}}</span>
                </div>
              </div>
              <div class="col-sm-6 border-right">
                <div class="description-block">
                  <h5 class="description-header">希望科目（受験対策）</h5>
                  <span class="description-text">{{$item["subject2"]}}</span>
                </div>
              </div>
              <div class="col-sm-6 border-right">
                <div class="description-block">
                  <h5 class="description-header">英会話希望講師</h5>
                  <span class="description-text">{{$item["english_teacher"]}}</span>
                </div>
              </div>
              <div class="col-sm-6 border-right">
                <div class="description-block">
                  <h5 class="description-header">授業形式</h5>
                  <span class="description-text">{{$item["course_type"]}}</span>
                </div>
              </div>
              <div class="col-sm-6 border-right">
                <div class="description-block">
                  <h5 class="description-header">ピアノのご経験</h5>
                  <span class="description-text">{{$item["piano_level"]}}</span>
                </div>
              </div>
              <div class="col-sm-6 border-right">
                <div class="description-block">
                  <h5 class="description-header">ご希望の習い事</h5>
                  <span class="description-text">{{$item["kids_lesson"]}}</span>
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
      <div class="col-12">
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
                    <th class="p-1 text-center">時間帯 / 曜日</th>
                    @foreach($attributes['lesson_week'] as $index => $name)
                    <th class="p-1 text-center lesson_week_label" atl="{{$index}}">
                       {{$name}}
                    </th>
                    @endforeach
                  </tr>
                  @foreach($attributes['lesson_time'] as $index => $name)
                  <tr class="">
                    <th class="p-1 text-center bg-gray text-sm lesson_week_time_label">{{$name}}</th>
                    @foreach($attributes['lesson_week'] as $week_code => $week_name)
                    <td class="p-1 text-center" id="lesson_{{$week_code}}_time_{{$index}}_name">
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
            <!-- /.row -->
          </div>
        </div>
        <!-- /.widget-user -->
      </div>
		</div>
	</div>
</section>

@endsection
