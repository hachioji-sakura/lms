@section('title')
受講料設定一覧
@endsection
@extends('dashboard.common')
@include($domain.'.menu')

@section('contents')
<section class="content">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title" id="charge_students">
            <i class="fa fa-yen-sign mr-1"></i>
            受講料設定一覧
          </h3>
          <ul class="pagination pagination-sm m-0 float-left text-sm">
            <li class="page-item">
              <a class="btn btn-info btn-sm" href="javascript:void(0);"  page_form="dialog" page_url="/tuitions/create?student_id={{$item->id}}" page_title="受講料設定登録">
                <i class="fa fa-plus"></i>
                <span class="btn-label">{{__('labels.add_button')}}</span>
              </a>
            </li>
          </ul>
          <div class="card-tools">
            <a class="page-link btn btn-float btn-default btn-sm" data-toggle="modal" data-target="#filter_form">
              <i class="fa fa-filter"></i>
              <span class="btn-label">{{__('labels.filter')}}</span>
            </a>
          </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body table-responsive p-0">
          @if(count($tuitions) > 0)
          <ul class="mailbox-attachments clearfix row">
            @foreach($tuitions as $tuition)
            <li class="col-12
            @if($tuition->is_enable()==false) bg-gray @endif
             p-0" accesskey="" target="">
              <div class="row p-2">
                <div class="col-12 col-md-9 mt-1">
                  <a href="javascript:void(0);" title="{{$tuition["id"]}}" page_title="{{__('labels.details')}}" page_form="dialog" page_url="/tuitions/{{$tuition["id"]}}" >
                    <i class="fa fa-cogs mx-1"></i>
                    {{$tuition["lesson_name"]}} / {{$tuition["course_type_name"]}} / {{$tuition["course_minutes_name"]}} / {{$tuition["grade_name"]}}
                  </a>
                </div>
                <div class="col-12 col-md-3 mt-1">
                  @if($tuition->is_enable()==false)
                    設定無効
                  @endif
                </div>
                <div class="col-12 col-md-6 mt-1 text-sm">
                  {{__('labels.teachers')}}：{{$tuition["teacher_name"]}} /  受講料： {{$tuition["tuition_money"]}}<br>
                  設定期間 : {{$tuition["enable_date"]}}
                </div>
                <div class="col-12 col-md-6 mt-2 text-sm text-right">
                  <a href="javascript:void(0);" title="{{$tuition["id"]}}" page_title="受講料設定変更" page_form="dialog" page_url="/tuitions/{{$tuition["id"]}}/edit" role="button" class="btn btn-success btn-sm mr-1">
                      <i class="fa fa-edit mr-1"></i>
                      編集
                  </a>
                  <a href="javascript:void(0);" title="{{$tuition["id"]}}" page_title="受講料設定削除" page_form="dialog" page_url="/tuitions/{{$tuition["id"]}}?action=delete" role="button" class="btn btn-danger btn-sm mr-1">
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

@component('components.list_filter', ['filter' => $filter, '_page' => $_page, '_line' => $_line, 'domain' => $domain, 'domain_name' => $domain_name, 'attributes'=>$attributes])
  @slot("search_form")
  <div class="col-12 col-md-4 mb-2">
    <label for="search_type" class="w-100">
      {{__('labels.lesson')}}
    </label>
    <div class="w-100">
      <select name="search_lesson[]" class="form-control select2" width=100% multiple="multiple" >
        @foreach($attributes['lesson'] as $index => $name)
          <option value="{{$index}}"
          @if(isset($filter['search_lesson']) && in_array($index, $filter['search_type'])==true)
          selected
          @endif
          >{{$name}}</option>
        @endforeach
      </select>
    </div>
  </div>
  @endslot
@endcomponent
@endsection
