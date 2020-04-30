@extends('dashboard.common')

@section('title_header',$domain_name)
@section('title',$domain_name)

@section('contents')
<div class="card">
  <div class="card-header">
    <div class="row">
      <div class="col-8">
        <h3 class="card-title">{{$target_user->details()->name()}}さんのタスク</h3>
      </div>
      <div class="col-4">
        <div class="float-right">
          <a href="/{{$target_user->details()->domain}}/{{$target_user->id}}/create_tasks" title="{{__('labels.add_button')}}" class="btn btn-primary">
            <i class="fa fa-plus"></i>
          </a>
        </div>
      </div>
    </div>

  </div>
  <div class="card-body p-0">
    <ul class="products-list product-list-in-card pl-2 pr-2">
      @foreach($items as $item)
      <li class="item">
        <div class="row align-items-center">
          <div class="col-1">
            <div class="product-img">
              <img src="{{$item->create_user->details()->icon()}}" class="img-size-50">
            </div>
          </div>
          <div class="col-11 float-left">
            <div class="product-info">
              <div class="row">
                <div class="col-6">
                  <a href="">
                    {{$item->title}}
                  </a>
                </div>
                <div class="col-6">
                  <small class="badge badge-danger float-right">
                    <i class="fas fa-stopwatch mr-1"></i>{{$item->end_schedule}}
                  </small>
                </div>
              </div>
              <div class="row">
                <div class="col-6 float-left">
                  <span class="product-description">
                    {{$item->remarks}}
                  </span>
                </div>
                <div class="col-6">
                  <div class="row float-right">
                    <div class="col-4">
                      <a href="/tasks/{{$item->id}}/edit" class="btn-sm btn-primary">
                        <i class="fas fa-play"></i>
                      </a>
                    </div>
                    <div class="col-4">
                      <a href="/tasks/{{$item->id}}/edit" class="btn-sm btn-info">
                        <i class="fa fa-edit"></i>
                      </a>
                    </div>
                    <div class="col-4">
                      <form method="POST" id="delete_task" action="/tasks/{{$item->id}}/delete">
                        @csrf
                        @method('DELETE')
                          <button type="button" form="delete_task" class="btn-sm btn-submit btn-primary">
                            <i class="fa fa-times"></i>
                          </button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-6">
                  <small class="badge badge-primary float-left">
                    <i class="fas fa-reply mr-1 "></i>{{$item->create_user->details()->name()}}
                  </small>
                </div>
                <div class="col-6">
                  <small class="float-right text-muted">
                    {{$item->created_at}}
                  </small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </li>
      @endforeach
    </ul>
  </div>
</div>
<script>
$(function(){
  base.pageSettinged("delete_task");
  $('button.btn-submit[form="delete_task"]').on('click', function(e){
    e.preventDefault();
    if(front.validateFormValue('delete_task')){
      $("form#delete_task").submit();
    }
  });
})

</script>
@endsection
