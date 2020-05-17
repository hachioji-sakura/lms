@section('tasks')
  <div class="col-md-4 col-sm-12">
    <div class="card mb-2">
      <div class="card-header">
        <div class="row">
          <div class="col-12">
            {{__('labels.status').__('labels.filter')}}
          </div>
        </div>
      </div>
      <div class="card-body">
        <select name="type" class="form-control select2"  onChange="location.href=value;">
          <option value="{{$target_user->id}}/tasks">{{__('labels.active')}}</option>
          @foreach(config('attribute.task_status') as $key => $value)
          <option value="{{$target_user->id}}/tasks?search_status={{$key}}" {{$request->query('search_status') == $key ? 'selected' : "" }}>{{$value}}</option>
          @endforeach
        </select>
      </div>
    </div>
  </div>
  <div class="col-md-8 col-sm-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-12">
            <h3 class="card-title">
              <i class="fa fa-tasks"></i>{{__('labels.tasks')}}
            </h3>
          </div>
        </div>
        <div class="card-tools">
          <a href="javascript:void(0)" page_form="dialog" page_title="{{__('labels.tasks').__('labels.add')}}" page_url="/tasks/create?student_id={{$target_user->id}}" title="{{__('labels.add_button')}}" role="button" class="btn btn-tool">
            <i class="fa fa-pen nav-icon"></i>
          </a>
          <a class="btn btn-tool" data-toggle="modal" data-target="#filter_form" id="filter_button">
            <i class="fa fa-filter"></i>
          </a>
          <button type="button" class="btn btn-default btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse">
              <i class="fa fa-minus"></i>
          </button>
          <!--
          <div class="paginate">
            {{$tasks->appends(Request::query())->links('components.paginate')}}
          </div>
          <!-- 検索 -->
        </div>
      </div>
      <div class="card-body p-0">
        @if(count($tasks)> 0)
        <ul class="products-list product-list-in-card pl-2 pr-2">
          @foreach($tasks as $item)
          <li class="item">
            <div class="row">
              <div class="col-8">
                <div class="row">
                  <div class="col-12">
                    <small class="badge badge-{{config('status_style')[$item->status]}}">
                      {{config('attribute.task_status')[$item->status]}}
                    </small>
                    @if(!empty($item->s3_url))
                       <i class="fas fa-paperclip"></i>
                    @endif
                  </div>
                  <div class="col-12 text-truncate">
                    <a href="/tasks/{{$item->id}}" title="{{__('labels.details')}}">
                      {{$item->title}}
                    </a>
                  </div>
                  <div class="col-12 text-truncate">
                    <small class="text-muted">
                      {{$item->body}}
                    </small>
                  </div>
                </div>
              </div>
              <div class="col-4">
                <div class="row">
                  <div class="col-12">
                    <a href="/tasks/{{$item->id}}" title="{{$item->id}}" class="btn btn-secondary btn-sm float-right">
                      <i class="fa fa-file-alt mr-1"></i>
                      {{__('labels.details')}}
                    </a>
                  </div>
                </div>
                <div class="row mt-1">
                  <div class="col-12">
                    <a href="javascript:void(0)" page_title="{{$item->title}}" page_form="dialog" page_url="/tasks/{{$item->id}}/edit" title="編集する" class="btn btn-sm btn-success float-right" role="button">
                      <i class="fa fa-edit"></i>
                      {{__('labels.edit')}}
                    </a>
                  </div>
                </div>
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
    @component('components.list_filter_message', ['filter' => $filter, '_page' => $_page, '_line' => $_line, 'domain' => 'tasks', 'domain_name' => __('labels.tasks'), 'attributes'=>$attributes])
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
        <div class="row">
          @foreach(config('attribute.task_status') as $key => $value)
          <div class="col-sm-12 col-md-2">
          <input class="frm-check-input icheck flat-green" type="checkbox" name="search_status[]"  value="{{$key}}">
          <label class="form-check-label">{{$value}}</label>
          </div>
          @endforeach
        </div>
      </div>
      @endslot
    @endcomponent
  </div>
@endsection
