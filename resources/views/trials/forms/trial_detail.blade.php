<div class="card card-widget mb-2">
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
          <h5 class="description-header">対応状況</h5>
          <span class="description-text">
            <small class="badge badge-{{config('status_style')[$item['status']]}} mx-2">
              {{$item["status_name"]}}
            </small>
          </span>
        </div>
      </div>
      @if($item->is_trial_lesson_complete()==true)
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
          <h5 class="description-header">ご希望のレッスン</h5>
          <span class="description-text">
            @foreach($item["tagdata"]['lesson'] as $label)
            <span class="text-xs mx-2">
              <small class="badge badge-info mt-1 mr-1">
                {{$label}}
              </small>
            </span>
            @endforeach
            @foreach($item["tagdata"]['lesson_week_count'] as $label)
              @if(!empty($label))
              <span class="text-xs mx-2">
                <small class="badge badge-info mt-1 mr-1">
                  週{{$label}}回
                </small>
              </span>
              @endif
            @endforeach

            @foreach($item["tagdata"]['course_minutes'] as $label)
              @if(!empty($label))
              <span class="text-xs mx-2">
                <small class="badge badge-info mt-1 mr-1">
                  {{$label}}授業
                </small>
              </span>
              @endif
            @endforeach

          </span>
        </div>
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
      <div class="col-sm-6 border-right">
        <div class="description-block">
          <span class="description-text">
            第１希望：<span class="text-xs mx-2">
              <small class="badge badge-secondary ">
                {{$item["date1"]}}
              </small>
            </span><br>
            第２希望：<span class="text-xs mx-2">
              <small class="badge badge-secondary ">
                {{$item["date2"]}}
              </small>
            </span><br>
            第３希望：<span class="text-xs mx-2">
              <small class="badge badge-secondary ">
                {{$item["date3"]}}
              </small>
            </span>
          </span>
        </div>
      </div>
      @isset($item["tagdata"]['lesson'][1])
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
      <div class="col-sm-6">
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
      @endisset
    </div>
    <div class="row">
      @isset($item["tagdata"]['lesson'][2])
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
        @isset($item["tagdata"]['english_talk_lesson'])
        <div class="col-sm-6">
          <div class="description-block">
            <h5 class="description-header">ご希望の英会話レッスン</h5>
            <span class="description-text">
              @foreach($item["tagdata"]['english_talk_lesson'] as $label)
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
        @isset($item["tagdata"]['english_talk_course_type'])
        <div class="col-sm-12">
          <div class="description-block">
            <h5 class="description-header">授業形式(英会話）</h5>
            <span class="description-text">
              @foreach($item["tagdata"]['english_talk_course_type'] as $label)
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
      @endisset
    </div>
    <div class="row">
      @isset($item["tagdata"]['lesson'][3])
        @isset($item["tagdata"]['piano_level'])
        <div class="col-sm-12">
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
      @endisset
    </div>
    <div class="row">
      @isset($item["tagdata"]['lesson'][4])
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
        @isset($item["tagdata"]['kids_lesson_course_type'])
        <div class="col-sm-6">
          <div class="description-block">
            <h5 class="description-header">授業形式(習い事）</h5>
            <span class="description-text">
              @foreach($item["tagdata"]['kids_lesson_course_type'] as $label)
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
      @endisset
    </div>
    <div class="row">      <div class="col-sm-12">
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
