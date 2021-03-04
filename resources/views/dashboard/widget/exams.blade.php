@section('exams')

<div class="card">
  <div class="card-header">
    <h3 class="card-title">
      <i class="fa fa-clipboard mr-1"></i>{{__('labels.exams')}}
    </h3>

    @if($grades->count() > 0)
    <select name="grades[]" width="100%" class="form-control select2" onChange="location.href=value;">
      @foreach($grades as $key => $name)
      <option value="/students/{{$item->id}}/exams?search_grade[]={{$key}}"
        @if(request()->has("search_grade") && in_array($key, request()->get("search_grade")))
         selected
        @endif
      >{{$name}}</option>
      @endforeach
    </select>
    @endif

    <div class="card-tools">
      <a class="btn btn-tool" href="javascript:void(0);" page_form="dialog" page_url="/exams/create?student_id={{$item->id}}" page_title="{{__('labels.exams')}}{{__('labels.add')}}">
          <i class="fa fa-pen nav-icon"></i>
      </a>
    </div>
  </div>
  <div class="card-body">
    <ul class="products-list product-list-in-card pl-2 pr-2" id="exams_list">
    @if($exams->count() > 0)
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
              @foreach($exams as $exam)
              <tr>
                <td >{{$exam->full_title}}</td>
                @foreach($subjects as $id => $name)
                <td >
                  @if(!empty($exam->exam_results->where('subject_id',$id)->first()))
                    {{$exam->exam_results->where('subject_id',$id)->first()->point}}/{{$exam->exam_results->where('subject_id',$id)->first()->max_point}}
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
        <h5>
          <i class="icon fa fa-exclamation-triangle"></i>
          {{__('messages.please_register',['target' => __('labels.exams')])}}
          <a class="btn btn-primary btn-block mt-2" href="javascript:void(0);" page_form="dialog" page_url="/exam_results/create?exam_id={{$exams->first()->id}}" page_title="{{__('labels.exams')}}{{__('labels.add')}}">
              <i class="fa fa-plus nav-icon"></i>
              {{__('labels.add_button')}}
          </a>
        </h5>
      @endif
      @foreach($exams as $exam)
        <div class="row mt-2">
          <div class="col-12">
            <h5>
              <label>{{$exam->full_title}}</label>
              <a class="btn btn-primary btn-sm  mr-1 my-1" href="javascript:void(0);" page_form="dialog" page_url="/exam_results/create?exam_id={{$exam->id}}" page_title="{{__('labels.exams')}}{{__('labels.add')}}">
                  <i class="fa fa-plus nav-icon"></i>
              </a>
              <a href="javascript:void(0);" page_title="{{__('labels.exams').__('labels.edit')}}" page_form="dialog" page_url="/exams/{{$exam->id}}/edit" role="button" class="btn btn-success btn-sm mr-1 my-1">
                <i class="fa fa-edit"></i>
              </a>
            </h5>
          </div>
          <div class="col-12" style="overflow-x:scroll;">
            @component('components.list',['items' => $exam->exam_results, 'fields' => $exam_fields, 'domain' => 'exam_results','domain_name' => __('labels.exam_results')])
            @endcomponent
          </div>
        </div>
      @endforeach
    @else
      <h5>
        <i class="icon fa fa-exclamation-triangle"></i>
        {{__('messages.please_register',['target' => __('labels.exams')])}}
        <a class="btn btn-primary btn-block mt-2" href="javascript:void(0);" page_form="dialog" page_url="/exams/create?student_id={{$item->id}}" page_title="{{__('labels.exams')}}{{__('labels.add')}}" role="button">
            <i class="fa fa-plus nav-icon"></i>
            {{__('labels.add_button')}}
        </a>
      </h5>
    @endif
    </ul>
  </div>
</div>
@endsection
