<div class="card card-widget mb-2">
  <div class="card-header">
    <i class="fa fa-envelope-open-text mr-1"></i>申込内容
    <span class="text-muted text-sm mx-2 float-right">
      <i class="fa fa-clock mr-1"></i>{{$item->created_date}}
    </span>
  </div>
  <div class="card-footer">
    <div class="row">
      <div class="col-sm-6 border-right">
        <div class="description-block">
          <h5 class="description-header">対応状況</h5>
          <span class="description-text">
            <small class="badge badge-{{config('status_style')[$item->status]}} mx-2">
              {{$item->status_name}}
            </small>
          </span>
        </div>
      </div>
      @if($item->is_request_lesson_complete()==true)
      <div class="col-sm-6 border-right">
        <div class="description-block">
          <h5 class="description-header">入会希望に関する連絡</h5>
          <span class="description-text">
              {{$item->entry_contact_send_date()}}
          </span>
        </div>
      </div>
      <div class="col-sm-6 border-right">
        <div class="description-block">
          <h5 class="description-header">授業開始希望日</h5>
          <span class="description-text">
              {{$item->dateweek_format($item->schedule_start_hope_date)}}
          </span>
        </div>
      </div>
      <div class="col-sm-6 border-right">
        <div class="description-block">
          <h5 class="description-header">入会案内連絡</h5>
          <span class="description-text">
              {{$item->entry_guidanced_send_date()}}
          </span>
        </div>
      </div>
      @endif
    </div>
  </div>


  <div class="card-footer">
    <div class="row">
      <div class="col-sm-6 border-right">
        <div class="description-block">
          <span class="description-text">
            @if($item->has_tag('parent_interview', 'true')==true)
            <small class="badge badge-danger">
              <i class="fa fa-exclamation-triangle mr-1"></i>
              入会説明希望あり
            </small>
            @else
            <small class="badge badge-secondary p-1 mr-1">
              入会説明希望なし
            </small>
            @endif
          </span>
        </div>

        <div class="description-block">
          <h5 class="description-header">ご希望のレッスン</h5>
          <span class="description-text">
            @foreach($item->get_tags('lesson') as $tag)
            <span class="text-xs mx-2">
              <small class="badge badge-info mt-1 mr-1">
                {{$tag->name()}}
              </small>
            </span>
            @endforeach
            <span class="text-xs mx-2">
              <small class="badge badge-info mt-1 mr-1">
                週{{$item->get_tag_value('lesson_week_count')}}回
              </small>
            </span>

            <span class="text-xs mx-2">
              <small class="badge badge-info mt-1 mr-1">
                {{$item->get_tag_value('course_minutes')}}授業
              </small>
            </span>

          </span>
        </div>
        <div class="description-block">
          <h5 class="description-header">ご希望の教室</h5>
          <span class="description-text">
            @foreach($item->get_tags('lesson_place') as $tag)
              <span class="text-xs mx-2">
                <small class="badge badge-success mt-1 mr-1">
                  {{$tag->name()}}
                </small>
              </span>
            @endforeach
          </span>
        </div>
      </div>
      <div class="col-sm-6 border-right">
        <div class="description-block">
          <span class="description-text">

            @foreach($item->request_dates as $d)
            第{{$d->sort_no}}希望：<span class="text-xs mx-2">
              <small class="badge badge-secondary ">
                {{$d->term}}
              </small>
            </span><br>
            @endforeach

          </span>
        </div>
      </div>
      @if($item->has_tag('lesson', 1) == true )
      <div class="col-sm-6 border-right">
        <div class="description-block">
          <h5 class="description-header">希望科目（補習）</h5>
          <span class="description-text">
            @if(count($item->get_subject())>0)
              @foreach($item->get_subject() as $label)
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
      <div class="col-sm-6">
        <div class="description-block">
          <h5 class="description-header">希望科目（受験対策）</h5>
          <span class="description-text">
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
          </span>
        </div>
      </div>
      @endif
    </div>
    @if($item->has_tag('lesson', 2) == true )
    <div class="row">
      <div class="col-sm-6 border-right">
        <div class="description-block">
          <h5 class="description-header">英会話希望講師</h5>
          <span class="description-text">
            <span class="text-xs mx-2">
              <small class="badge badge-secondary mt-1 mr-1">
                {{$item->get_tag_name('english_teacher')}}
              </small>
            </span>
          </span>
        </div>
      </div>
      <div class="col-sm-6">
        <div class="description-block">
          <h5 class="description-header">ご希望の英会話レッスン</h5>
          <span class="description-text">
            @foreach($item->get_tags('english_talk_lesson') as $tag)
            <span class="text-xs mx-2">
              <small class="badge badge-secondary mt-1 mr-1">
                {{$tag->name()}}
              </small>
            </span>
            @endforeach
          </span>
        </div>
      </div>
      <div class="col-sm-12">
        <div class="description-block">
          <h5 class="description-header">授業形式(英会話）</h5>
          <span class="description-text">
            <span class="text-xs mx-2">
              <small class="badge badge-secondary mt-1 mr-1">
                {{$item->get_tag_name('english_talk_course_type')}}
              </small>
            </span>
          </span>
        </div>
      </div>
    </div>
    @endif
    @if($item->has_tag('lesson', 3) == true )
    <div class="row">
      <div class="col-sm-12">
        <div class="description-block">
          <h5 class="description-header">ピアノのご経験</h5>
          <span class="description-text">
            <span class="text-xs mx-2">
              <small class="badge badge-secondary mt-1 mr-1">
                {{$item->get_tag_name('piano_level')}}
              </small>
            </span>
          </span>
        </div>
      </div>
    </div>
    @endif
    @if($item->has_tag('lesson', 4) == true )
    <div class="row">
      <div class="col-sm-6 border-right">
        <div class="description-block">
          <h5 class="description-header">ご希望の習い事</h5>
          <span class="description-text">
            @foreach($item->get_tags('kids_lesson') as $tag)
            <span class="text-xs mx-2">
              <small class="badge badge-secondary mt-1 mr-1">
                {{$tag->name()}}
              </small>
            </span>
            @endforeach
          </span>
        </div>
      </div>
      <div class="col-sm-6">
        <div class="description-block">
          <h5 class="description-header">授業形式(習い事）</h5>
          <span class="description-text">
            @foreach($item->get_tags('kids_lesson_course_type') as $tag)
            <span class="text-xs mx-2">
              <small class="badge badge-secondary mt-1 mr-1">
                {{$tag->name()}}
              </small>
            </span>
            @endforeach
          </span>
        </div>
      </div>
    </div>
    @endif
    <div class="row">
      <div class="col-sm-12">
        <div class="description-block">
          <h5 class="description-header">ご要望</h5>
          <span class="description-text">
            {!!nl2br($item->remark_full())!!}
          </span>
        </div>
      </div>
    </div>
  </div>
</div>
