@section('title')
  {{$domain_name}}一覧
@endsection
@extends('dashboard.common')

@section('page_sidemenu')
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
  <li class="nav-item hr-1">
    @if(isset($teacher_id) && $teacher_id > 0)
    <a href="javascript:void(0);" page_title="{{$domain_name}}登録" page_form="dialog" page_url="/{{$domain}}/create?teacher_id={{$teacher_id}}" class="nav-link">
    @else
    <a href="javascript:void(0);" page_title="{{$domain_name}}登録" page_form="dialog" page_url="/{{$domain}}/create" class="nav-link">
    @endif
      <i class="fa fa-plus nav-icon"></i>{{$domain_name}} {{__('labels.add')}}
    </a>
  </li>
  <li class="nav-item has-treeview menu-open mt-2">
    <a href="#" class="nav-link">
      <i class="nav-icon fa fa-filter"></i>
      <p>
        {{__('labels.filter')}}
        <i class="right fa fa-angle-left"></i>
      </p>
    </a>
    <ul class="nav nav-treeview">
      <li class="nav-item">
         <a href="/{{$domain}}" class="nav-link">
           <i class="fa fa-calendar nav-icon"></i>すべて
         </a>
       </li>
    </ul>
  </li>
</ul>
@endsection


@section('contents')


<div class="card-body p-0">
  <section class="content">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title" id="student_groups">
              <i class="fa fa-calendar mr-1"></i>
              {{__('labels.student_groups_list')}}
            </h3>
            <div class="card-title text-sm">
              @if(count($items) > 0)
              {{$items->appends(Request::query())->links('components.paginate')}}
              @endif
                <ul class="pagination pagination-sm m-0 float-left text-sm">
                  <li class="page-item">
                    <a class="btn btn-info btn-sm" href="javascript:void(0);"  page_form="dialog" page_url="/student_groups/create" page_title="{{$domain_name}} {{__('labels.add')}}">
                      <i class="fa fa-plus"></i>
                      <span class="btn-label">{{__('labels.add_button')}}</span>
                    </a>
                  </li>
                </ul>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body table-responsive p-0">
            @if(count($items) > 0)
            <ul class="mailbox-attachments clearfix row">
              @foreach($items as $item)
              <li class="col-12 p-0" accesskey="" target="">
                <div class="row p-2">
                  <div class="col-7 col-lg-4 col-md-4">
                    <a href="javascript:void(0);" title="{{$item["id"]}}" page_title="{{__('labels.details')}}" page_form="dialog" page_url="/student_groups/{{$item["id"]}}" >
                      <i class="fa fa-users mx-1"></i>{{$item["title"]}}
                      <br>
                      <i class="fa fa-star mx-1"></i>{{$item["type_name"]}}
                    </a>
                  </div>
                  <div class="col-5 col-lg-4 col-md-4">
                    @foreach($item["students"] as $member)
                        <a alt="student_name" href="/students/{{$member->id}}" class="mr-2" target=_blank>
                          <i class="fa fa-user-graduate"></i>
                          {{$member->name()}}
                        </a><br>
                    @endforeach
                  </div>
                  <div class="col-12 col-lg-4 col-md-4 text-sm mt-1">
  	                <a href="javascript:void(0);" title="{{$item["id"]}}" page_title="生徒グループ編集" page_form="dialog" page_url="/student_groups/{{$item["id"]}}/edit" role="button" class="btn btn-success btn-sm mr-1">
  	                    <i class="fa fa-edit mr-1"></i>
  	                    編集
  	                </a>
  	                <a href="javascript:void(0);" title="{{$item["id"]}}" page_title="生徒グループ削除" page_form="dialog" page_url="/student_groups/{{$item["id"]}}?action=delete" role="button" class="btn btn-danger btn-sm mr-1">
  	                    <i class="fa fa-trash mr-1"></i>
  	                    削除
  	                </a>
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
      </div>
    </div>
  </section>
</div>
@component('components.list_filter', ['_page' => $_page, '_line' => $_line, 'domain' => $domain, 'domain_name' => $domain_name, 'attributes'=>$attributes])



@slot("search_form")
  <div class="col-12 col-md-4">
    <label for="charge_subject" class="w-100">
      {{__('labels.type')}}
    </label>
    <div class="w-100">
      <select name="search_type[]" class="form-control select2" width=100% placeholder="検索タイプ" multiple="multiple" >
        @foreach($attributes['course_type'] as $index=>$name)
          @if($index==="single")
            @continue
          @endif
          <option value="{{$index}}"
          @if(isset($filter['search_type']) && in_array($index, $filter['search_type'])==true)
          selected
          @endif
          >{{$name}}</option>
        @endforeach
      </select>
    </div>
  </div>
  <div class="col-12 col-md-4">
    <label for="charge_subject" class="w-100">
      {{__('labels.search_keyword')}}
    </label>
    <div class="input-group mb-3">
      <input type="text" name="search_word" class="form-control" placeholder="{{__('labels.search_keyword')}}" value="{{$search_word}}" style="width:140px;">
    </div>
  @endslot
@endcomponent
@endsection


@section('page_footer')
  <dt>
    <a class="btn btn-app"  href="javascript:void(0);" page_title="{{$domain_name}}登録" page_form="dialog" page_url="{{$domain}}/create">
      <i class="fa fa-plus"></i>{{$domain_name}}登録
    </a>
  </dt>
@endsection
