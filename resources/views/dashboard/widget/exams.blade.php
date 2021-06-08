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
    <div class="row">
    @if($exams->count() > 0)
    @foreach($exams as $exam)
      {{-- 試験段組 --}}
      <div class="col-12 col-md-6 mt-2">
        {{-- 試験詳細部分 --}}
            <div class="small-box" style="background:rgba(200,200,200,0.2);">
              <div class="inner">
                <small class="badge badge-info float-left mr-2">
                  {{$exam->semester_name}}
                </small>
                <small class="badge badge-danger float-left">
                  {{$exam->name}}
                </small>
                <br>
                <h1 class="text-lg">
                  @if(!empty($exam->s3_url))
                  <a href="{{$exam->s3_url}}" target="_blank" class="underline">
                  <i class="fa fa-link mr-1"></i>
                  @endif
                    {{$exam->sum_point_per_max}}  点
                  @if(!empty($exam->s3_url))
                  </a>
                  @endif
                </h1>
                @if(!empty($exam->s3_url))
                  </a>
                </span>
                @endif
                <p>
                  {{$exam->result_count}} 科目
                </p>
                <div class="row">
                  <div class="col-12 col-md-6 mt-2">
                    <a href="javascript:void(0);" page_title="答案を追加する" page_form="dialog" page_url="/exam_results/create?exam_id={{$exam->id}}" role="button" class="btn btn-outline-primary btn-block btn-sm ml-1">
                      <i class="fa fa-plus mr-1"></i>答案追加
                    </a>
                  </div>
                  <div class="col-12 col-md-6 text-right mt-2">
                    <a href="javascript:void(0);" page_title="{{__('labels.exams')}} {{__('labels.edit')}}" page_form="dialog" page_url="/exams/{{$exam->id}}/edit" role="button" class="btn btn-success btn-sm ml-1">
                      <i class="fa fa-edit"></i>
                    </a>
                    {{-- TODO 論理削除ロジック対応でコメントアウト解除する
                    <a href="javascript:void(0);" page_title="{{__('labels.exams')}} {{__('labels.delete')}}" page_form="dialog" page_url="/exams/{{$exam->id}}?action=delete" role="button" class="btn btn-danger btn-sm ml-1">
                      <i class="fa fa-trash"></i>
                    </a>
                    --}}
                  </div>
                </div>
              </div>
              <div class="icon">
                <i class="fa fa-chart-bar"></i>
              </div>
              <a href="/students/{{$item->id}}/exams/{{$exam->id}}" class="small-box-footer text-dark">
                答案一覧 <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
      </div>
  @endforeach
  </div>
  </ul>
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
