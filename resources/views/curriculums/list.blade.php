@extends('dashboard.common')

@section('title_header',__('labels.'.$domain).__('labels.list'))
@section('title', __('labels.'.$domain).__('labels.list'))

@section('page_sidemenu')
 {{--@include('curriculums.menu')--}}
@endsection

@section('contents')
<div class="card">
  <div class="card-header">
    <div class="row">
      <div class="col-12">
        @if(isset($target_user))
        <h3 class="card-title">{{$target_user->details()->name()}}さん
        </h3>
        {{__('labels.'.$domain).__('labels.list')}}
        @else
        <h3 class="card-title">
          <i class="fa fa-{{$domain}}"></i>
          {{__('labels.'.$domain).__('labels.list')}}
        </h3>
        @endif

        <div class="d-none d-sm-block">
            <a href="javascript:void(0)" page_form="dialog" page_title="{{__('labels.'.$domain).__('labels.add')}}" page_url="/{{$domain}}/create" title="{{__('labels.add_button')}}" role="button" class="btn btn-primary btn-sm">
            <i class="fa fa-plus"></i>
            {{__('labels.add_button')}}
          </a>
        </div>
      </div>
    </div>
    <div class="card-tools">
      @component('components.search_word', ['search_word' => $search_word])
      @endcomponent
      {{$items->appends(Request::query())->links('components.paginate')}}
      <!-- 検索 -->
    </div>
  </div>

  <div class="card-body p-0">
    @if(count($items)> 0)
    <ul class="products-list product-list-in-card pl-2 pr-2">
      @foreach($items as $item)
      <li class="item">
        <div class="row">
          <div class="col-8">
            @component($domain.'.components.list_left',[
            'item' => $item,
            'domain' => $domain
            ])
            @endcomponent
          </div>

          <div class="col-4">
            <div class="row">
              <div class="col-12">
                <a href="javascript:void(0)" title="{{__('labels.details')}}" page_form="dialog" page_title="{{$item->name}}" page_url="/{{$domain}}/{{$item->id}}" class="float-right btn btn-sm btn-secondary" role="button">
                  <i class="fa fa-file-alt mr-1"></i>
                  {{__('labels.details')}}
                </a>
              </div>
            </div>
            <div class="row mt-1">
              <div class="col-12">
                <a href="javascript:void(0)" page_title="{{$item->name}}" page_form="dialog" page_url="/{{$domain}}/{{$item->id}}/edit" title="{{__('labels.edit_button')}}" class="btn btn-sm btn-success float-right" role="button">
                  <i class="fa fa-edit"></i>
                  {{__('labels.edit')}}
                </a>
              </div>
            </div>
            <div class="row mt-1">
              <div class="col-12">
                <a href="javascript:void(0)" page_title="{{__('messages.confirm_delete')}}" page_form="dialog" page_url="/{{$domain}}/{{$item->id}}/delete" title="{{__('labels.delete_button')}}" class="btn btn-sm btn-danger float-right" role="button">
                  <i class="fa fa-trash mr-1"></i>
                  {{__('labels.delete')}}
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
  </div>
  @endslot
@endcomponent

@endsection
