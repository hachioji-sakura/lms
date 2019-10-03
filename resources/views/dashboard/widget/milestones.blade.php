@section('milestones')
<div class="card">
  <div class="card-header">
    <h3 class="card-title">
      <i class="fa fa-flag mr-1"></i>{{__('labels.milestones')}}
    </h3>
  </div>
  <!-- /.card-header -->
  <div class="card-body">
    <div id="milestone_list">
      @foreach($milestones as $milestone)
      <?php $milestone = $milestone->details(); ?>
      <div class="small-box bg-light">
        <div class="inner">
          <a data-toggle="collapse" data-parent="#milestone_list" href="#m1" class="" aria-expanded="true">
            <h3 title='{{$milestone->title}}'>{{ str_limit($milestone->title, 42, '...') }}</h3>
          </a>
          <div id="m1" class="panel-collapse in collapse show" style="">
            <p>{{$milestone->body}}</p>
            <div class="icon">
              @if($milestone->type==='study')
              <i class="fa fa-book-reader"></i>
              @elseif($milestone->type==='examination')
              <i class="fa fa-clipboard-check"></i>
              @elseif($milestone->type==='promotion')
              <i class="fa fa-school"></i>
              @else
              <i class="fa fa-star"></i>
              @endif
            </div>
            <a href="#" class="small-box-footer">
              <span class="text-sm">
                {{__('labels.created')}}：{{$milestone["create_user_name"]}} / {{$milestone["created_date"]}}
              </span>
            </a>

            <span class="float-right mr-1">
              <a href="javascript:void(0);" page_title="{{__('labels.milestones')}}{{__('labels.edit')}}" page_form="dialog" page_url="/milestones/{{$milestone->id}}/edit?origin={{$domain}}&item_id={{$item->id}}" role="button" class="btn btn-default btn-sm float-left mr-1">
                <i class="fa fa-edit"></i>
              </a>
              <a href="javascript:void(0);" page_title="{{__('labels.milestones')}}{{__('labels.delete')}}" page_form="dialog" page_url="/milestones/{{$milestone->id}}?origin={{$domain}}&item_id={{$item->id}}&action=delete" role="button" class="btn btn-default btn-sm float-left mr-1">
                <i class="fa fa-trash"></i>
              </a>
            </span>
          </div>
        </div>
      </div>
      @endforeach
    </div>
    <!-- /.card-body -->
  </div>
</div>
@endsection
