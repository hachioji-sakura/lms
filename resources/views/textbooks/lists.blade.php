@section('title')
  {{$domain_name}} {{__('labels.list')}}
@endsection

@section('list_filter')

  @component('components.list_filter', ['filter' => $filter, '_page' => $_page, '_line' => $_line, 'domain' => $domain, 'domain_name' => $domain_name, 'attributes'=>$attributes])
    @slot("search_form")
    <div class="col-12 col-md-6">
      <div class="form-group">
        <label for='search_publisher_id' class="w-100">
          {{__('labels.publisher_name')}}
        </label>
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class="fa fa-search-plus"></i></span>
          </div>
          <select name='search_publisher_id' class="form-control select2" width="80%">
            <option value="">
              {{__('labels.selectable')}}
            </option>
            @foreach($publishers as $publisher)
            <option value="{{ $publisher->id }}"
              @if(request()->search_publisher_id == $publisher->id)
              selected
              @endif>
              {{$publisher->name}}
            </option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-6">
      <div class="form-group">
        <label for='search_supplier_id' class="w-100">
          {{__('labels.supplier_name')}}
        </label>
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class="fa fa-search-plus"></i></span>
          </div>
          <select name='search_supplier_id' class="form-control select2" width="80%">
            <option value="">
              {{__('labels.selectable')}}
            </option>
            @foreach($suppliers as $supplier)
            <option value="{{ $supplier->id }}"
              @if(request()->search_supplier_id == $supplier->id)
              selected
              @endif>
              {{$supplier->name}}
            </option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-6">
      <div class="form-group">
        <label for='search_difficulty' class="w-100">
          {{__('labels.difficulty')}}
        </label>
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class="fa fa-search-plus"></i></span>
          </div>
          <select
            name='search_difficulty'
            class="form-control select2" width="80%">
            <option value="">
              {{__('labels.selectable')}}
            </option>
            @foreach(config('attribute.difficulty') as $key => $value)
              <option value="{{$key}}"
              @if(request()->search_difficulty == $key)
                selected
              @endif>
               {{$value}}
              </option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
    <div class="col-12">
      <div class="form-group">
        <label for="search_subjects" class="w-100">
          {{__('labels.subjects')}}
        </label>
        <select name="search_subjects[]" class="w-100 form-control select2" width="100%" multiple="multiple" >
          @foreach($subjects as $subject)
            <option value="{{$subject->id}}"
              @if(isset(request()->search_subjects) && in_array($subject->id, request()->search_subjects,true))
               selected
              @endif>
              {{$subject->name}}
            </option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="col-12">
      <div class="form-group">
        <label for="search_grade" class="w-100">
          {{__('labels.grade')}}
        </label>
        <div class="col-6">
          <select name="search_grade[]" class="w-100 form-control select2" width=100% multiple="multiple" >
            @foreach($grades as $grade)
              <option value="{{$grade->attribute_value}}"
                @if(isset(request()->search_grade) && in_array($grade->attribute_name, request()->search_grade,true))
                  selected
                @endif>
                {{$grade->attribute_name}}
              </option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
    <div class="col-12 mb-2">
      <div class="form-group">
      <label for="search_keyword" class="w-100">
        {{__('labels.search_keyword')}}
      </label>
      <input type="text" name="search_keyword" class="form-control" placeholder="" inputtype=""
         value = "{{request()->search_keyword}}"
      >
      </div>
    </div>

    @endslot
  @endcomponent

@endsection
@section('page_sidemenu')
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
  <li class="nav-item hr-1">
    <a href="javascript:void(0);" page_title="{{$domain_name}}登録" page_form="dialog" page_url="/{{$domain}}/create" class="nav-link">
      <i class="fa fa-plus nav-icon"></i>{{$domain_name}} {{__('labels.add')}}
    </a>
  </li>
</ul>
@endsection

@section('page_footer')
<dt>
  <a class="btn btn-app"  href="javascript:void(0);" page_title="{{$domain_name}} {{__('labels.add')}}" page_form="dialog" page_url="{{$domain}}/create">
    <i class="fa fa-plus"></i>{{$domain_name}} {{__('labels.add')}}
  </a>
</dt>
@endsection

@section('list_pager')
<div class="card-title text-sm">
  {{$items->appends(Request::query())->links('components.paginate')}}
</div>
@endsection

@include('dashboard.lists')
@extends('dashboard.common')
