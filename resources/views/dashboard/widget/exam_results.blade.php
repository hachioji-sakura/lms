@section('exam_results')
<div class="card">
  <div class="card-header">
      <h3 class="card-title">
        <i class="fa fa-clipboard mr-1"></i>{{__('labels.exams')}}
      </h3>
      <select name="exams[]" width="100%" class="form-control select2" onChange="location.href=value;">
        @foreach($exams as $ex)
        <option value="/students/{{$item->id}}/exams/{{$ex->id}}"
          @if($exam->id == $ex->id)
           selected
          @endif
        >{{$ex->semester_name}}:{{$ex->name}}</option>
        @endforeach
      </select>
      <a class="btn btn-info btn-sm mt-2" href="/{{$domain}}/{{$item->id}}/exams?seach_grade[]={{$exam->grade}}">
        <i class="fa fa-arrow-alt-circle-left"></i>
        {{__('labels.back_button')}}
      </a>
      <div class="card-tools">
        <a class="btn btn-tool float-right mt-2" href="javascript:void(0);" page_form="dialog" page_url="/exam_results/create?exam_id={{$exam->id}}" page_title="{{__('labels.exams')}}{{__('labels.add')}}">
            <i class="fa fa-pen nav-icon"></i>
        </a>
    </div>
  </div>
  <div class="card-body">
    <div class="col-12">
      <div style="overflow-x:scroll;">
      @component("components.list",['fields' => $exam_result_fields, 'domain' => 'exam_results', 'domain_name' => __('labels.exam_results'), 'items' => $exam_results ])
      @endcomponent
      </div>
    </div>
  </div>
</div>
@endsection
