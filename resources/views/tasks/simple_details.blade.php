<div class="">
  {{--詳細単体では表示しなくなったので、コメントアウト
  @if(isset($item->body))
  <div class="row">
    <div class="col-12">
      <label>{{__('labels.details')}}</label>
      <div class="form-group">
          {!!nl2br($item->body)!!}
      </div>
    </div>
  </div>
  @endif
  --}}
  <div class="row">
    <div class="col-12">
      <label>{{__('labels.details')}}</label>
      <div class="form-group">
          {!!nl2br($item->full_title)!!}
      </div>
    </div>
  </div>

    <div class="row mt-1">
      <div class="col-6">
        <label class="w-100">{{__('labels.status')}}</label>
        <small class="badge badge-{{config('status_style')[$item->status]}}">
          {{config('attribute.task_status')[$item->status]}}
        </small>
      </div>
      @if( isset($item->start_date) || isset($item->end_date) )
      <div class="col-6">
        <label class="w-100">{{__('labels.progress_button')}}・{{__('labels.done_button')}}</label>
        <div class="form-group">
          {{$item->dateweek_format($item->start_date,'Y/m/d')}}～{{$item->dateweek_format($item->end_date,'Y/m/d')}}
        </div>
      </div>
      @endif
    </div>

    @if(isset($item->milestone_id))
    <div class="row mt-2">
      <div class="col-12">
        <label>{{__('labels.milestones')}}</label>
        <div class="form-group">
          {{$item->milestones->title}}
        </div>
      </div>
    </div>
    @endif
    <div class="row mt-2">
      <div class="col-6">
        <label>{{__('labels.create_user')}}</label>
        <div class="form-group">
          {{$item->create_user->details()->name()}}
        </div>
      </div>
      @if( isset($item->start_schedule) || isset($item->end_schedule) )
      <div class="col-6">
        <label>{{__('labels.task_schedule')}}</label>
        <div class="form-group">
          {{$item->dateweek_format($item->start_schedule,'Y/m/d')}}～{{$item->dateweek_format($item->end_schedule,'Y/m/d')}}
        </div>
      </div>
      @endif
    </div>
    <div class="row">
      <div class="col-6">
        <label>{{__('labels.subjects')}}</label>
        <div class="form-group">
          @foreach($item->curriculums as $curriculum)
            @foreach($curriculum->subjects as $subject)
            <small class="badge badge-primary">
              {{$subject->name}}
            </small>
            @endforeach
          @endforeach
        </div>
      </div>
      <div class="col-6">
        <label>{{__('labels.curriculums')}}</label>
        <div class="form-group">
          @foreach($item->curriculums as $curriculum)
          <small class="badge badge-primary">
            {{$curriculum->name}}
          </small>
          @endforeach
        </div>
      </div>
      <div class="col-12">
        @if( $item->task_reviews->count() > 0)
          <label>{{__('labels.task_understanding')}}</label>
          @foreach($item->task_reviews as $review)
            <div class="col-12">
              @for($i=1;$i<=$review->evaluation;$i++)
              <span class="fa fa-star fa-xs" title="{{$review->evaluation}}" style="color:{{$review->create_user->details()->role == 'student' ? 'green' : 'orange'}};"></span>
              @endfor
              @for($i=1;$i<=4-$review->evaluation;$i++)
              <span class="far fa-star fa-xs" style="color:gray"></span>
              @endfor
              <small class="text-muted">
                /{{$review->create_user->details()->name()}}
              </small>
            </div>
          @endforeach
        @endif
      </div>
    </div>

    @if(!empty($item->s3_url))
    <a href="{{$item->s3_url}}" target="_blank">
      <i class="fa fa-link mr-1"></i>
      {{$item->s3_alias}}
    </a>
    @endif
</div>
<div class="row mt-3">
  <div class="col-12">
    <button type="reset" class="btn btn-sm btn-secondary btn-block">
      <i class="fa fa-times mr-1"></i>
      {{__('labels.close_button')}}
    </button>
  </div>
</div>
