@section('list_filter')
  @component('components.list_filter', ['filter' => $filter, '_page' => $_page, '_line' => $_line, 'domain' => $domain, 'domain_name' => $domain_name, 'attributes'=>$attributes])
    @slot("search_form")
      @component('textbooks.forms.search_form', ['grades' => $grades , 'subjects' => $subjects ])
      @endcomponent
      <input type="hidden" name="student_id" value="{{request()->student_id}}">
    @endslot
  @endcomponent
@endsection

@section('student_textbooks')
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">{{__('labels.student_textbooks')}}</h3>
      <div class="card-tools">
        {{$textbooks->appends(Request::query())->links('students.textbooks.paginate')}}
        <a href="javascript:void(0)" page_form="dialog" page_title="{{!empty(request()->get('search_type')) ? __('labels.'.request()->get('search_type')).__('labels.add') : __('labels.learning_record').__('labels.add')}}" page_url="/students/{{$item->id}}/textbooks/create" title="{{__('labels.add_button')}}" role="button" class="btn btn-tool">
          <i class="fa fa-pen nav-icon"></i>
        </a>
        <a class="btn btn-tool" data-toggle="modal" data-target="#filter_form" id="filter_button">
          <i class="fa fa-filter"></i>
        </a>
      </div>
    </div>

    <div class="card-body table-responsive p-0">
      @component('components.list', ['items' => $textbooks, 'fields' => $fields, 'domain' => $domain, 'domain_name' => $domain_name, 'bulk_action' => isset($bulk_action) ? $bulk_action : null])
      @endcomponent
    </div>
  </div>
  @yield('list_filter')
@endsection
