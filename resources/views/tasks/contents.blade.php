<div class="card">
  <div class="card-header">
    <div class="row">
      <div class="col-12">
        @if(isset($target_user))
        <h3 class="card-title">{{$target_user->details()->name()}}さん
        </h3>
        {{__('labels.tasks').__('labels.list')}}
        @else
        <h3 class="card-title">{{__('labels.tasks').__('labels.list')}}</h3>
        @endif
        @if(isset($target_user))
        <div class="d-none d-sm-block">
            <a href="javascript:void(0)" page_form="dialog" page_title="{{__('labels.tasks').__('labels.add')}}" page_url="/tasks/create?student_id={{$target_user->id}}" title="{{__('labels.add_button')}}" role="button" class="btn btn-primary btn-sm">
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
      <div class="paginate">
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
                <a href="javascript:void(0)" page_title="{{$item->title}}" page_form="dialog" page_url="/tasks/{{$item->id}}/edit" title="編集する" class="btn btn-sm btn-info float-right" role="button">
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
    <div class="row">
      @foreach(config('attribute.task_status') as $key => $value)
      <div class="col-sm-12 col-md-2">
      <input class="frm-check-input icheck flat-green" type="checkbox" name="search_status[]" id="evaluation" value="{{$key}}">
      <label class="form-check-label">{{$value}}</label>
      </div>
      @endforeach
    </div>
  </div>
  @endslot
@endcomponent
