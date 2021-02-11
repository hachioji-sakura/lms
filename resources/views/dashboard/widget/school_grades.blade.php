@section('school_grades')

<div class="card">
  <div class="card-header">
    <h3 class="card-title">
      <i class="fa fa-flag mr-1"></i>{{__('labels.school_grades')}}
    </h3>
    <div class="card-tools">
      <a class="btn btn-tool" href="javascript:void(0);" page_form="dialog" page_url="/school_grades/create?student_id={{$item->id}}" page_title="{{__('labels.school_grades')}}{{__('labels.add')}}">
          <i class="fa fa-pen nav-icon"></i>
      </a>
    </div>
  </div>
  <div class="card-body">
    <ul class="products-list product-list-in-card pl-2 pr-2" id="school_grades_list">
      <?php $is_exist=false; ?>
      @foreach($school_grades as $school_grade)
      <?php
        $is_exist = true;
        //dd($item);
      ?>
      <li class="item">
        <div class="">

          <a href="javascript:void(0);" page_title="{{__('labels.school_grades')}}" page_form="dialog" page_url="/school_grades/{{$school_grade->id}}" class="product-title">
            <b class="text-lg" style="font-size:110%;">
            {{ str_limit($school_grade->title, 42, '...') }}
            </b>
{{--
            <span class="badge
            @if($milestone->type==='study')
            badge-primary
            @elseif($milestone->type==='examination')
            badge-warning
            @elseif($milestone->type==='promotion')
            badge-danger
            @else
            badge-secondary
            @endif
             float-right">
              {{$milestone["type_name"]}}
            </span>
--}}
          </a>

            <span class="product-description">
              {{$school_grade->remark}} ( {{$school_grade->grade_name}} / {{$school_grade->semester_name}} )

            </span>
        </div>
      </li>
      @endforeach

      @if($is_exist == false)
      <div class="alert">
        <h4><i class="icon fa fa-exclamation-triangle"></i>{{__('labels.no_data')}}</h4>
      </div>
      @endif
    </ul>
  </div>
</div>
@endsection
