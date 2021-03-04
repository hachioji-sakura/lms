@section('title')
{{$domain_name}}ダッシュボード
@endsection
@extends('teachers.page')
@include($domain.'.menu')


@section('text_materials')
<div class="row">
  <div class="col-12 mb-2">
    <div class="card card-widget">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fa fa-book mr-1"></i>{{__('labels.text_materials')}}
        </h3>
        <div class="card-tools">
          <a class="btn btn-tool" href="javascript:void(0);" page_form="dialog" page_url="/text_materials/create?origin={{$domain}}&target_user_id={{$item->user_id}}" page_title="{{__('labels.text_materials')}}{{__('labels.add')}}">
            <i class="fa fa-pen nav-icon"></i>
          </a>
          <a class="btn btn-tool" data-toggle="modal" data-target="#filter_form" id="filter_button">
            <i class="fa fa-filter nav-icon"></i>
          </a>
        </div>

      </div>
      <div class="card-body">
        <ul class="products-list product-list-in-card pl-2 pr-2" id="text_material_list">
          <?php $is_exist=false; ?>
          @foreach($text_materials as $text_material)
          <?php
          $is_exist = true;
          ?>

          <li class="col-lg-4 col-md-4 col-12 " accesskey="" target="">
            <div class="row">
              <div class="col-12 text-center">
                <a href="javascript:void(0);" page_title="{{__('labels.text_materials')}}" page_form="dialog" page_url="/text_materials/{{$text_material->id}}" class="product-title">
                  <img src="https://s3-ap-northeast-1.amazonaws.com/lms-file/user_icon/ZMWiOJkoB0mGuwojZPpfFYho8oIXUdBCxlvqmIhK.svg" class="img-circle elevation-2 mw-128px w-50">
                </a>
              </div>
            </div>
            <div class="row my-2">
              <div class="col-12 text-lg text-center">
                <a href="javascript:void(0);" page_title="{{__('labels.text_materials')}}" page_form="dialog" page_url="/text_materials/{{$text_material->id}}" class="product-title">
                  {{ str_limit($text_material->name, 42, '...') }}
                </a>
                <span class="text-xs ml-1">
                  <small class="badge badge-success mt-1 mr-1">
                    入会済
                  </small>
                </span>
                <br>
                <span class="text-xs ml-1">
                  <small class="badge badge-primary mt-1 mr-1">
                    英会話,ピアノ
                  </small>
                </span>
                <span class="text-xs ml-1">
                  <small class="badge badge-primary mt-1 mr-1">
                    小2
                  </small>
                </span>
              </div>
            </div>
            <div class="row my-2">
              <div class="col-12">
                  <a href="{{$text_material->s3_url}}" target="_blank" role="button" class="btn btn-info btn-sm float-left mr-1">
                    <i class="fa fa-cloud-download-alt"></i>
                  </a>
                  <a href="javascript:void(0);" page_title="{{__('labels.text_materials')}}{{__('labels.edit')}}" page_form="dialog" page_url="/text_materials/{{$text_material->id}}/edit?origin={{$domain}}&item_id={{$item->id}}" role="button" class="btn btn-default btn-sm float-left mr-1">
                    <i class="fa fa-edit"></i>
                  </a>
                  <a href="javascript:void(0);" page_title="{{__('labels.text_materials')}}{{__('labels.delete')}}" page_form="dialog" page_url="/text_materials/{{$text_material->id}}?origin={{$domain}}&item_id={{$item->id}}&action=delete" role="button" class="btn btn-default btn-sm float-left mr-1">
                    <i class="fa fa-trash"></i>
                  </a>
                </span>
                <span class="float-left mr-2 text-muted text-sm mt-2" style="font-size:.6rem;">
                  <i class="fa fa-clock mr-1"></i>{{$text_material["create_user_name"]}} / {{$text_material["created_date"]}}
                </span>
              </div>
            </div>
          </li>



          <li class="item">
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" page_title="{{__('labels.text_materials')}}" page_form="dialog" page_url="/text_materials/{{$text_material->id}}" class="product-title">
                <div class="info-box">
                  <span class="info-box-icon bg-secondary">
                    <i class="fa fa-users"></i>
                  </span>
                  <div class="info-box-content text-dark">
                    <b class="info-box-text text-lg">{{ str_limit($text_material->name, 42, '...') }}</b>
                    <span class="text-sm">{{$text_material->description}}</span>
                  </div>
                </div>
              </a>
              <div >
                <span class="float-right mr-1">
                  <a href="javascript:void(0);" page_title="{{__('labels.text_materials')}}{{__('labels.edit')}}" page_form="dialog" page_url="/text_materials/{{$text_material->id}}/edit?origin={{$domain}}&item_id={{$item->id}}" role="button" class="btn btn-default btn-sm float-left mr-1">
                    <i class="fa fa-edit"></i>
                  </a>
                  <a href="javascript:void(0);" page_title="{{__('labels.text_materials')}}{{__('labels.delete')}}" page_form="dialog" page_url="/text_materials/{{$text_material->id}}?origin={{$domain}}&item_id={{$item->id}}&action=delete" role="button" class="btn btn-default btn-sm float-left mr-1">
                    <i class="fa fa-trash"></i>
                  </a>
                </span>
                <span class="float-left mr-2 text-muted text-sm mt-2" style="font-size:.6rem;">
                  <i class="fa fa-clock mr-1"></i>{{$text_material["create_user_name"]}} / {{$text_material["created_date"]}}
                </span>
              </div>
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
  </div>
  @component('components.list_filter', ['filter' => $filter, '_page' => $_page, '_line' => $_line, 'domain' => $domain, 'domain_name' => $domain_name, 'attributes'=>$attributes])
  @slot("search_form")
  <input type="hidden" name="check_user_id" value="{{$user->user_id}}">
  <div class="col-6 col-md-4">
    <div class="form-group">
      <label for="search_from_date" class="w-100">
        {{__('labels.date')}}(FROM)
      </label>
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text"><i class="fa fa-calendar"></i></span>
        </div>
        <input type="text" id="search_from_date" name="search_from_date" class="form-control float-left" uitype="datepicker" placeholder="2000/01/01"
        @if(isset($filter['search_from_date']))
        value="{{$filter['search_from_date']}}"
        @endif
        >
      </div>
    </div>
  </div>
  <div class="col-6 col-md-4">
    <div class="form-group">
      <label for="search_to_date" class="w-100">
        {{__('labels.date')}}(TO)
      </label>
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text"><i class="fa fa-calendar"></i></span>
        </div>
        <input type="text" id="search_to_date" name="search_to_date" class="form-control float-left" uitype="datepicker" placeholder="2000/01/01"
        @if(isset($filter['search_to_date']))
        value="{{$filter['search_to_date']}}"
        @endif
        >
      </div>
    </div>
  </div>
  <div class="col-12 mb-2">
    <label for="search_status" class="w-100">
      {{__('labels.comments')}}
      {{__('labels.type')}}
    </label>
    <div class="w-100">
      <select name="search_comment_type[]" class="form-control select2" width=100% placeholder="検索タイプ" multiple="multiple" >
        @foreach(config('attribute.comment_type') as $index => $name)
        <option value="{{$index}}"
        @if(isset($filter['comment_filter']['search_comment_type']) && in_array($index, $filter['comment_filter']['search_comment_type'])==true)
        selected
        @endif
        >{{$name}}</option>
        @endforeach
      </select>
    </div>
  </div>
  <div class="col-12 mb-2">
    <div class="form-group">
      <label for="search_keyword" class="w-100">
        {{__('labels.search_keyword')}}
      </label>
      <input type="text" name="search_keyword" class="form-control" placeholder="{{__('labels.search_keyword')}}"
      @if(isset($filter['search_keyword']))
      value="{{$filter['search_keyword']}}"
      @endif
      >
    </div>
  </div>
  <div class="col-12 mb-2">
    <div class="form-group">
      <label for="is_asc" class="w-100">
        {{__('labels.other')}}
      </label>
      <label class="mx-2">
        <input type="checkbox" value="1" name="is_asc" class="icheck flat-green"
        @if(isset($filter['sort']['is_asc']) && $filter['sort']['is_asc']==true)
        checked
        @endif
        >{{__('labels.date')}} {{__('labels.asc')}}
      </label>
      <label class="mx-2">
        <input type="checkbox" value="1" name="is_checked_only" class="icheck flat-green"
        @if(isset($filter['comment_filter']['is_checked_only']) && $filter['comment_filter']['is_checked_only']==1)
        checked
        @endif
        >{{__('labels.checked_only')}}
      </label>
      <label class="mx-2">
        <input type="checkbox" value="1" name="is_unchecked_only" class="icheck flat-green"
        @if(isset($filter['comment_filter']['is_unchecked_only']) && $filter['comment_filter']['is_unchecked_only']==1)
        checked
        @endif
        >{{__('labels.unchecked_only')}}
      </label>
    </div>
  </div>
  @endslot
  @endcomponent
</div>
@endsection

@section('sub_contents')
@yield('text_materials')
@endsection
