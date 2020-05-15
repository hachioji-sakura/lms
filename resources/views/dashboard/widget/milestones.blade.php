@section('milestones')
<div class="card">
  <div class="card-header">
    <h3 class="card-title">
      <i class="fa fa-flag mr-1"></i>{{__('labels.milestones')}}
    </h3>
    <div class="card-tools">
      <a class="btn btn-tool" href="javascript:void(0);" page_form="dialog" page_url="/milestones/create?origin={{$domain}}&item_id={{$item->id}}" page_title="{{__('labels.milestones')}}{{__('labels.add')}}">
          <i class="fa fa-pen nav-icon"></i>
      </a>
      <button type="button" class="btn btn-default btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse">
          <i class="fa fa-minus"></i>
      </button>
    </div>
  </div>
  <div class="card-body">
    <ul class="products-list product-list-in-card pl-2 pr-2" id="milestone_list">
      <?php $is_exist=false; ?>
      @foreach($milestones as $milestone)
      <?php
        $milestone = $milestone->details();
        $is_exist = true;
      ?>
      <li class="item">
        <div class="">
          <a href="javascript:void(0);" page_title="{{__('labels.milestones')}}" page_form="dialog" page_url="/milestones/{{$milestone->id}}" class="product-title">
            <b class="text-lg" style="font-size:110%;">
            {{ str_limit($milestone->title, 42, '...') }}
            </b>
            <span class="badge
            @if($milestone->type==='study')
            badge-primary
            @elseif($milestone->type==='examination')
            badge-warning
            @elseif($milestone->type==='promotion')
            badge-danger
            @else
            badge-secondary
            @endif
             float-right">
              {{$milestone["type_name"]}}
            </span>
          </a>
            <span class="product-description">
              {{$milestone->body}}
            </span>
          <span class="float-right mr-1">
            <a href="javascript:void(0);" page_title="{{__('labels.milestones')}}{{__('labels.edit')}}" page_form="dialog" page_url="/milestones/{{$milestone->id}}/edit?origin={{$domain}}&item_id={{$item->id}}" role="button" class="btn btn-default btn-sm float-left mr-1">
              <i class="fa fa-edit"></i>
            </a>
            <a href="javascript:void(0);" page_title="{{__('labels.milestones')}}{{__('labels.delete')}}" page_form="dialog" page_url="/milestones/{{$milestone->id}}?origin={{$domain}}&item_id={{$item->id}}&action=delete" role="button" class="btn btn-default btn-sm float-left mr-1">
              <i class="fa fa-trash"></i>
            </a>
          </span>
          <span class="float-left mr-2 text-muted text-sm mt-2" style="font-size:.6rem;">
            <i class="fa fa-clock mr-1"></i>{{$milestone["create_user_name"]}} / {{$milestone["created_date"]}}
          </span>
        </div>
      </li>
      @endforeach
      @if($is_exist == false)
      <div class="alert">
        <h4><i class="icon fa fa-exclamation-triangle"></i>{{__('labels.no_data')}}</h4>
      </div>
      @endif
    </ul>
  </div>
</div>
@endsection
