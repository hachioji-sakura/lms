@section('title')
  {{$domain_name}} {{__('labels.list')}}
@endsection

@section('list_filter')
  @component('components.list_filter', ['filter' => $filter, '_page' => $_page, '_line' => $_line, 'domain' => $domain, 'domain_name' => $domain_name, 'attributes'=>$attributes])
    @slot("search_form")
    @component('textbooks.forms.subject', ['prefix'=>'search_','textbook'=> $textbook??null,'subjects' => $subjects,'textbook_subjects' => $textbook->subject_list??null]); @endcomponent
    @component('textbooks.forms.grade', ['prefix'=>'search_','grades' => $grades,'textbook_grades' => $textbook->grade_list??null]); @endcomponent
    <div class="col-12 mb-2">
      <div class="form-group">
      <label for="search_keyword" class="w-100">
        {{__('labels.search_keyword')}}
      </label>
      <input type="text" name="search_keyword" class="form-control" placeholder="" inputtype=""
        @if(!empty(request()->search_keyword))
          value = "{{request()->search_keyword}}"
        @elseif(!empty(request()->search_word))
          value = "{{request()->search_word}}"
        @endif
      >
      </div>
    </div>
    <input type="hidden" name="school_id" value="{{request()->school_id}}">
    @endslot
  @endcomponent

@endsection
@section('page_sidemenu')
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
  <li class="nav-item hr-1">
    <a href="javascript:void(0);" page_title="{{$domain_name}}登録" page_form="dialog" page_url="/{{$domain}}/create?school_id={{$school_id}}" class="nav-link">
      <i class="fa fa-plus nav-icon"></i>{{$domain_name}} {{__('labels.add')}}
    </a>
  </li>
</ul>
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
  <li class="nav-item hr-1">
    <a href="javascript:void(0);" page_title="{{__('labels.textbooks')}}登録" page_form="dialog" page_url="/textbooks/create?school_id={{$school_id}}" class="nav-link">
      <i class="fa fa-folder-plus nav-icon"></i>{{__('labels.textbooks')}} {{__('labels.add')}}
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
