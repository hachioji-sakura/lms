<div class="card">
  <div class="card-header">
    <div class="row">
      <div class="col-12">
        @if(isset($target_user))
        <h3 class="card-title">{{$target_user->details()->name()}}さん
        </h3>
        {{__('labels.tasks').__('labels.list')}}
        @else
        <h3 class="card-title">タスク一覧</h3>
        @endif
        @if(isset($target_user))
        <div class="">
          <a href="javascript:void(0)" page_form="dialog" page_title="For {{$target_user->details()->name}}" page_url="/{{$target_user->details()->domain}}/{{$target_user->id}}/create_tasks" title="{{__('labels.add_button')}}" role="button"  class="btn btn-primary btn-sm">
            <i class="fa fa-plus"></i>
            {{__('labels.add_button')}}
          </a>
        </div>
        @endif
      </div>
    </div>
    <div class="card-tools">
      @component('components.search_word', ['search_word' => $search_word])
      @endcomponent
      <div class="pagenate">
        {{$items->appends(Request::query())->links('components.paginate')}}
      </div>
      <!-- 検索 -->
    </div>
  </div>
  <div class="card-body p-0">
    @if(count($items)> 0)
    <ul class="products-list product-list-in-card pl-2 pr-2">
      @foreach($items as $item)
      <li class="item {{$item->status == "cancel" ? 'bg-info' : ''}}">
        <div class="row">
          <div class="col-6">
            <div class="row">
              <div class="col-12 text-truncate">
                <a href="/tasks/{{$item->id}}" title="{{__('labels.details')}}">
                  {{$item->title}}
                </a>
              </div>
              <div class="col-12 text-truncate">
                <small class="text-muted">
                  {{$item->remarks}}
                </small>
              </div>
            </div>
          </div>
          <div class="col-3">
            <small class="badge badge-{{config('status_style')[$item->status]}}">
              {{config('attribute.task_status')[$item->status]}}
            </small>
            <br/>
            <small class="badge badge-danger">
              <i class="fas fa-stopwatch mr-1"></i>{{$item->end_schedule}}
            </small>
          </div>
          <div class="col-3">
            <a href="/tasks/{{$item->id}}" title="{{$item->id}}" class="btn btn-secondary btn-sm">
              <i class="fa fa-file-alt mr-1"></i>
              {{__('labels.details')}}
            </a>
          </div>
        </div>
      </li>
      @endforeach
    </ul>
    @else
    <div class="alert">
      <h4><i class="icon fa fa-exclamation-triangle"></i>{{__('labels.no_data')}}</h4>
    </div>
    @endif
  </div>
</div>
@component('components.list_filter_message', ['filter' => $filter, '_page' => $_page, '_line' => $_line, 'domain' => $domain, 'domain_name' => $domain_name, 'attributes'=>$attributes])
  @slot('search_form')
  <div class="col-12 mb-2">
      <label for="search_word" class="w-100">
        {{__('labels.search_keyword')}}
      </label>
      <input type="text" name="search_word" class="form-control" placeholder="" inputtype=""
      @isset($filter['search_keyword'])
      value = "{{$filter['search_keyword']}}"
      @endisset
      >
  </div>
  <div class="col-12">
    <label for="search_status" class="w-100">
      {{__('labels.status')}}
    </label>
      @foreach(config('attribute.task_status') as $key => $value)
      <input class="frm-check-input icheck flat-green" type="checkbox" name="search_status[]" id="evaluation" value="{{$key}}">
      <label class="form-check-label">{{$value}}</label>
      @endforeach
  </div>
  @endslot
@endcomponent
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
