<div class="">
    <small class="badge badge-{{config('status_style')[$item->status]}}">
      {{config('attribute.task_status')[$item->status]}}
    </small>
    <div class="row mt-1">
      <div class="col-12">
        @if(!empty($item->stars))
        @for($i=1;$i<=$item->stars;$i++)
        <span class="fa fa-star" style="color:orange;"></span>
        @endfor
        @for($i=1;$i<=5-$item->stars;$i++)
        <span class="far fa-star"></span>
        @endfor
        ({{$item->stars}})
        @endif
      </div>
      <div class="col-12">
        <small class="text-muted">
          @if( isset($item->start_date) || isset($item->end_date) )
          {{$item->start_date}}~{{$item->end_date}}
          @endif
        </small>
      </div>
    </div>
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
    @if(isset($item->milestone_id))
    <div class="row">
      <div class="col-6">
        <label>{{__('labels.milestones')}}</label>
        <div class="form-group">
          {{$item->milestones->title}}
        </div>
      </div>
      <div class="col-6">
        <label>{{__('labels.type')}}</label>
        <div class="form-group">
          {{config('attribute.task_type')[$item->type]}}
        </div>
      </div>
    </div>
    @endif
    <div class="row">
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

    @if(!empty($item->s3_url))
    <a href="{{$item->s3_url}}">
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
