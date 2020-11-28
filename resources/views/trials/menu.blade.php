@section('page_sidemenu')

<div class="user-panel mt-3 pb-3 mb-3 d-flex">
  <div class="image mt-1">
    <img src="{{$item->student->user->icon()}}" class="img-circle elevation-2" alt="User Image">
  </div>
  <div class="info">
    <a href="/{{$domain}}/{{$item->id}}/" class="d-block text-light">
      <ruby style="ruby-overhang: none">
        <rb>{{$item->student->name()}}</rb>
        <rt>{{$item->student->kana()}}</rt>
      </ruby>
    </a>
    <small class="badge badge-info mx-2">
      {{$item->student->gender()}}
    </small>
    <small class="badge badge-info mx-2">
      {{$item->student->grade()}}
    </small><br>
    <small class="badge badge-info mx-2">
      {{$item->student->school_name()}}
    </small>
  </div>
</div>
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
  <li class="nav-item has-treeview menu-open">
    <a href="#" class="nav-link">
      <i class="nav-icon fa fa-envelope-open-text"></i>
      <p>
        対応状況
        <small class="badge badge-{{config('status_style')[$item['status']]}} mx-2">
          {{$item["status_name"]}}
        </small>
        <i class="right fa fa-angle-left"></i>
      </p>
    </a>
    <ul class="nav nav-treeview p-2">
      @foreach($item->request_dates as $d)
      <li class="nav-item text-light ml-2 mb-2">
        第{{$d->sort_no}}希望：<span class="text-xs mx-2">
          <small class="badge badge-secondary ">
            {{$d->term}}
          </small>
        </span>
      </li>
      @endforeach
      <li class="nav-item text-light ml-2 hr-1 bd-light mb-2">
        レッスン：
        <div class="nav-link w-100">
          @foreach($item->get_tags('lesson') as $tag)
          <span class="text-xs mx-2">
            <small class="badge badge-info mt-1 mr-1">
              {{$tag->name()}}
            </small>
          </span>
          @endforeach
          <span class="text-xs mx-2">
            <small class="badge badge-info mt-1 mr-1">
              週{{$item->get_tag_name('lesson_week_count')}}回
            </small>
          </span>
          <span class="text-xs mx-2">
            <small class="badge badge-info mt-1 mr-1">
              {{$item->get_tag_name('course_minutes')}}授業
            </small>
          </span>
        </div>
      </li>
      <li class="nav-item text-light ml-2 hr-1 bd-light mb-2">
        教室：
        <div class="nav-link w-100">
          @foreach($item->get_tags('lesson_place') as $tag)
            <span class="text-xs mx-2">
              <small class="badge badge-success mt-1 mr-1">
                {{$tag->name()}}
              </small>
            </span>
          @endforeach
        </div>
      </li>
    </ul>
  </li>
  @foreach($item->get_calendar() as $calendar)
  <li class="nav-item has-treeview menu-open text-light">
    <a href="#" class="nav-link">
    <p>
      <i class="nav-icon fa fa-clock"></i>
      体験予定
      <small class="badge badge-{{config('status_style')[$calendar->status]}} mx-2">
        {{$calendar->status_name()}}
      </small>
      <i class="right fa fa-angle-left"></i>
    </p>
    </a>
    <ul class="nav nav-treeview p-2">
        <li class="nav-item">
          講師：
          <div class="nav-link w-100">
            <span class="text-xs mx-2">
              <small class="badge badge-secondary ">
                <i class="fa fa-user-tie mr-1"></i>
                {{$calendar->user->name}}
              </small>
            </span>
          </div>
          予定日時：
          <div class="nav-link w-100">
            <span class="text-xs mx-2">
              <small class="badge badge-secondary ">
                {{$calendar->datetime}}
              </small>
            </span>
          </div>
        </li>
    </ul>
  </li>
  @endforeach
  <li class="nav-item has-treeview menu-open">
    <a href="#" class="nav-link">
    <i class="nav-icon fa fa-envelope-open-text"></i>
    <p>
      科目
      <i class="right fa fa-angle-left"></i>
    </p>
    </a>
    <ul class="nav nav-treeview">
      <li class="nav-item">
        <div class="nav-link w-100">
          補習
        </div>
        @if(count($item->get_subject())>0)
          @foreach($item->get_subject() as $label)
              <span class="text-xs mx-2">
                <small class="badge badge-primary mt-1 mr-1">
                  {{$label}}
                </small>
              </span>
          @endforeach
        @else
        <div class="nav-link w-100 ml-2 ">
          なし
        </div>
        @endif
        <div class="nav-link w-100">
          受験
        </div>
        @if(count($item->get_subject(true))>0)
          @foreach($item->get_subject(true) as $label)
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
        @if(count($item->get_tags('english_talk_lesson')) > 0)
          <div class="nav-link w-100">
            英会話
          </div>
          @foreach($item->get_tags('english_talk_lesson') as $tag)
          <span class="text-xs mx-2">
            <small class="badge badge-info mt-1 mr-1">
              {{$tag->name()}}
            </small>
          </span>
          @endforeach
        @endif
      </li>
        @if(count($item->get_tags('kids_lesson')) > 0)
          <div class="nav-link w-100">
            習い事
          </div>
          @foreach($item->get_tags('kids_lesson') as $tag)
          <span class="text-xs mx-2">
            <small class="badge badge-info mt-1 mr-1">
              {{$tag->name()}}
            </small>
          </span>
          @endforeach
        @endif
      </li>
    </ul>
  </li>
</ul>
@endsection
@section('page_footer')
{{-- まだ対応しない
  <dt>
    <a class="btn btn-app" href="javascript:void(0);" accesskey="task_add" disabled>
      <i class="fa fa-plus"></i>タスク登録
    </a>
  </dt>
--}}
@endsection
