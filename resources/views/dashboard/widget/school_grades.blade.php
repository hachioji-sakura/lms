@section('school_grades')

<div class="card">
  <div class="card-header">
    <h3 class="card-title">
      <i class="fa fa-chart-line mr-1"></i>{{__('labels.school_grades')}}
    </h3>
    @if($grades->count() > 0)
    <select name="grades[]" width="100%" class="form-control" onChange="location.href=value;">
      <option value=" ">{{__("labels.selectable")}}</option>
      @foreach($grades as $key => $name)
      <option value="/students/{{$item->id}}/school_grades?search_grade[]={{$key}}"
        @if(request()->has("search_grade") && in_array($key, request()->get("search_grade")))
         selected
        @endif
      >{{$name}}</option>
      @endforeach
    </select>
    @endif
    <div class="card-tools">
      <a class="btn btn-tool" href="javascript:void(0);" page_form="dialog" page_url="/school_grades/create?student_id={{$item->id}}" page_title="{{__('labels.school_grades')}}{{__('labels.add')}}">
          <i class="fa fa-pen nav-icon"></i>
      </a>
    </div>
  </div>
  <div class="card-body">
    <ul class="products-list product-list-in-card pl-2 pr-2" id="school_grades_list">
    @if($school_grades->count() > 0)
      @if(count($subjects) > 0 )
      <div style="overflow-x:scroll;">
        <table class="table table-hover">
          <thead>
            <tr>
              <th ></th>
              @foreach($subjects as $id => $name)
              <th >
                {{$name}}
              </th>
              @endforeach
            </tr>
          </thead>
          <tbody>
            @foreach($school_grades as $school_grade)
            <tr>
              <td >{{$school_grade->semester_name}}</td>
              @foreach($subjects as $id => $name)
              <td >
                @if(!empty($school_grade->school_grade_reports->where('subject_id',$id)->first()))
                  {{$school_grade->school_grade_reports->where('subject_id',$id)->first()->report_point_name}}
                @else
                -
                @endif
              </td>
              @endforeach
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @else
      {{--TODO:編集対象は登録されている最初の奴でいいかな？--}}
        <h5>
          <i class="icon fa fa-exclamation-triangle"></i>
          {{__('messages.please_register',['target' => __('labels.school_grade_reports')])}}
          <a class="btn btn-primary btn-block mt-2" href="javascript:void(0);" page_form="dialog" page_url="/school_grades/{{$school_grades->first()->id}}/edit" page_title="{{__('labels.school_grades')}}{{__('labels.add')}}">
              <i class="fa fa-plus nav-icon"></i>
              {{__('labels.add_button')}}
          </a>
        </h5>
      @endif
      <div class="mt-5" style="overflow-x:scroll;">
        @component('components.list',['items' => $school_grades, 'fields' => $school_grade_fields, 'domain' => 'school_grades','domain_name' => __('labels.school_grades')])
        @endcomponent
      </div>
    @else
      <h5>
        <i class="icon fa fa-exclamation-triangle"></i>
        {{__('messages.please_register',['target' => __('labels.school_grades')])}}
        <a class="btn btn-primary btn-block mt-2" href="javascript:void(0);" page_form="dialog" page_url="/school_grades/create?student_id={{$item->id}}" page_title="{{__('labels.school_grades')}}{{__('labels.add')}}" role="button">
            <i class="fa fa-plus nav-icon"></i>
            {{__('labels.add_button')}}
        </a>
      </h5>
    @endif
    </ul>
  </div>
</div>
<script>
$(function(){
//    $('select[name="grades[]"]').change();
});
</script>
@endsection
