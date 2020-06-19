@section('tasks')
<!--
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
          <option value="{{$item->id}}/tasks">{{__('labels.active')}}</option>
          @foreach(config('attribute.task_status') as $key => $value)
          <option value="/{{$domain}}/{{$item->id}}/tasks?search_status={{$key}}" {{request()->query('search_status') == $key ? 'selected' : "" }}>{{$value}}</option>
          @endforeach
        </select>
      </div>
    </div>
  </div>
-->
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-12">
            <h3 class="card-title">
              <i class="fa fa-{{!empty(request()->get('search_type')) && request()->get('search_type') == 'homework' ? 'book-reader': 'list-alt'}} mr-1"></i>{{!empty(request()->get('search_type')) ? __('labels.'.request()->get('search_type')) : __('labels.tasks')}}
            </h3>
          </div>
        </div>
        <div class="card-tools">
          <a href="javascript:void(0)" page_form="dialog" page_title="{{!empty(request()->get('search_type')) ? __('labels.'.request()->get('search_type')).__('labels.add') : __('labels.tasks').__('labels.add')}}" page_url="/tasks/create?student_id={{$item->id}}&task_type={{request()->get('search_type')}}" title="{{__('labels.add_button')}}" role="button" class="btn btn-tool">
            <i class="fa fa-pen nav-icon"></i>
          </a>
          @if(!empty(request()->get('search_type') != 'class_record') && request()->get('search_type') != 'class_record')
          <a href="/students/{{$item->id}}/tasks?search_status[]=done&search_type={{request()->get('search_type')}}" class="btn btn-tool">
            <i class="fa fa-tasks"></i>
          </a>
          @endif
          <a class="btn btn-tool" data-toggle="modal" data-target="#filter_form" id="filter_button">
            <i class="fa fa-filter"></i>
          </a>
          {{--
          <div class="paginate">
            {{$tasks->appends(Request::query())->links('components.paginate')}}
          </div>
          --}}
          <!-- 検索 -->
        </div>
      </div>
      <div class="card-body p-0">
        @if(count($tasks)> 0)
        <ul class="products-list product-list-in-card pl-2 pr-2">
          @foreach($tasks as $item)
          <li class="item">
            <div class="row">
              <div class="col-12 ">
                <div class="row">
                  <div class="col-3">
                    <small class="badge badge-{{config('status_style')[$item->status]}}">
                      {{config('attribute.task_status')[$item->status]}}
                    </small>
                  </div>
                  <div class="col-9">

                  </div>
                  <div class="col-12 col-md-4 text-truncate">
                    <a href="javascript:void(0)" title="{{__('labels.details')}}" page_form="dialog" page_title="{{$item->title}}" page_url="/tasks/{{$item->id}}/detail_dialog" role="button">
                      {{$item->title}}
                    </a>
                  </div>
                  <div class="col-12 col-md-8 text-truncate">
                    <div class="row">
                      <div class="col-6">
                        <small class="text-muted">
                          {{$item->body}}
                        </small>
                      </div>
                      <div class="col-6">

                      </div>
                    </div>
                  </div>
                  <div class="col-12 col-md-8">
                    <div class=" row">
                    @if( $item->task_reviews->count() > 0)
                      @foreach($item->task_reviews as $review)
                      <div class="col-6">
                        <small class="text-muted">
                          {{$review->create_user->details()->name()}}
                        </small>
                        <br/>
                        @for($i=1;$i<=$review->evaluation;$i++)
                        <span class="fa fa-star" style="color:{{$review->create_user->details()->role == 'student' ? 'green' : 'orange'}};"></span>
                        @endfor
                        @for($i=1;$i<=5-$review->evaluation;$i++)
                        <span class="far fa-star"></span>
                        @endfor
                      </div>
                      @endforeach
                    @endif
                    </div>
                  </div>
                  @if(!empty($item->s3_url))
                  <div class="col-12">
                    <a href="{{$item->s3_url}}" target="_blank">
                      <i class="fa fa-link fa-sm"></i>
                      <small>
                        {{$item->s3_alias}}
                      </small>
                    </a>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                @component('tasks.components.buttons',[
                  'item' => $item,
                  'is_footer' => false,
                ])
                @endcomponent
                <div class="float-right">
                  <a href="javascript:void(0)" title="{{__('labels.details')}}" page_form="dialog" page_title="{{$item->title}}" page_url="/tasks/{{$item->id}}/detail_dialog" class="btn btn-secondary btn-sm mr-1" role="button">
                    <i class="fa fa-file-alt"></i>
                  </a>
                  <a href="javascript:void(0)" page_title="{{$item->title}}" page_form="dialog" page_url="/tasks/{{$item->id}}/edit" title="{{__('labels.edit')}}" class="btn btn-sm btn-success mr-1" role="button">
                    <i class="fa fa-edit"></i>
                  </a>
                @if($item->status != "cancel")
                  <a href="javascript:void(0)" title="{{__('labels.delete')}}" page_form="dialog" page_title="{{__('messages.confirm_delete')}}" page_url="/tasks/{{$item->id}}/cancel" class="btn btn-sm btn-{{config('status_style')['cancel']}} mr-1" role="button">
                    <i class="fa fa-trash"></i>
                  </a>
                @endif
                </div>
              </div>
            </div>
            <div class="col-12">
              <small class="text-muted float-right">
                <i class="fa fa-clock"></i>
                {{$item->create_user->details()->name()}}/
                {{$item->dateweek_format($item->created_at,'Y/m/d')}}  {{date('H:i',strtotime($item->created_at))}}
              </small>
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
          @if( !empty(request()->get('search_word')))
          value = "{{request()->get('search_word')}}"
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
          <input class="frm-check-input icheck flat-green" type="checkbox" name="search_status[]" id="status_{{$key}}" value="{{$key}}" {{!empty(request()->get('search_status')) && in_array($key, request()->get('search_status'))? 'checked':''}}>
          <label class="form-check-label" for="status_{{$key}}" id="status_{{$key}}">{{$value}}</label>
          </div>
          @endforeach
        </div>
      </div>

      <div class="col-12 mt-2">

        <div class="row">
        <div class="col-6 col-md-4">
          <label for="search_evaluation" class="w-100">
            {{__('labels.evaluation')}}(min)
          </label>
          <div class="input-group">
            <select class="form-control select2" width=100% name="eval_min_value">
              <option value="">{{__('labels.selectable')}}</option>
              @foreach(config('attribute.task_review_evaluation') as $key => $value)
              <option value="{{$key}}" {{!empty(request()->get('eval_min_value')) && request()->get('eval_min_value') == $key ? 'selected' :''}}>{{$value}}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="col-6 col-md-4">
          <label for="search_evaluation" class="w-100">
            {{__('labels.evaluation')}}(max)
          </label>
          <div class="input-group">
            <select class="form-control select2" width=100% name="eval_max_value">
              <option value="">{{__('labels.selectable')}}</option>
              @foreach(config('attribute.task_review_evaluation') as $key => $value)
              <option value="{{$key}}" {{!empty(request()->get('eval_max_value')) && request()->get('eval_max_value') == $key ? 'selected' :''}}>{{$value}}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
      </div>

      <div class="col-6 col-md-4">
        <div class="form-group">
          <label for="search_from_date" class="w-100">
            {{__('labels.date')}}(FROM)
          </label>
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text"><i class="fa fa-calendar"></i></span>
            </div>
            <input type="text" id="search_from_date" name="search_from_date" class="form-control float-left" uitype="datepicker" placeholder="2000/01/01" autocomplete="off" inputtype="date" value="{{!empty(request()->get('search_from_date')) ? request()->get('search_from_date') : ''}}">
          </div>
        </div>
      </div>
      <div class="col-6 col-md-4">
        <div class="form-group">
          <label for="search_to_date" class="w-100">
            {{__('labels.date')}}(TO)
          </label>
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text"><i class="fa fa-calendar"></i></span>
            </div>
            <input type="text" id="search_to_date" name="search_to_date" class="form-control float-left" uitype="datepicker" placeholder="2000/01/01" autocomplete="off" inputtype="date" value={{!empty(request()->get('search_to_date')) ? request()->get('search_to_date') : ''}}>
          </div>
        </div>
      </div>
      <input type="hidden" name="search_type" value="{{request()->search_type}}">
      @endslot
    @endcomponent
  </div>
@endsection
