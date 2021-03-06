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
        <ul class="mailbox-attachments clearfix row">
          <?php $is_exist=false; ?>
          @foreach($text_materials as $text_material)
          <?php
          $is_exist = true;
          ?>

          <li class="col-lg-4 col-md-4 col-12" accesskey="" target="">
            <div class="row">
              <div class="col-12 text-center">
                <a href="javascript:void(0);" page_title="{{__('labels.text_materials')}}" page_form="dialog" page_url="/text_materials/{{$text_material->id}}" class="product-title">
                  <img
                    @if($text_material->is_movie()==true)
                    src="/svg/movie.svg"
                    @elseif($text_material->is_music()==true)
                    src="/svg/music.svg"
                    @elseif($text_material->is_image()==true)
                    src="/svg/image.svg"
                    @elseif($text_material->is_pdf()==true)
                    src="/svg/pdf.svg"
                    @else
                    src="/svg/note.svg"
                    @endif
                    class="img-circle elevation-2 mw-128px w-50" />
                </a>
              </div>
            </div>
            <div class="row my-2">
              <div class="col-12 text-lg text-center">
                <a href="javascript:void(0);" page_title="{{$text_material->name}}" page_form="dialog" page_url="/text_materials/{{$text_material->id}}" class="product-title">
                  {{ str_limit($text_material->name, 42, '...') }}
                </a>
                @if($text_material->is_publiced()==true)
                <span class="text-sm ml-1">
                  <small class="badge badge-danger mt-1 mr-1 flo">
                    {{__('labels.public')}}
                  </small>
                </span>
                @elseif($text_material->target_user_id != $item->user_id)
                <span class="text-sm ml-1">
                  <small class="badge badge-warning mt-1 mr-1 flo">
                    {{__('labels.share')}}
                  </small>
                </span>
                @endif
              </div>
            </div>
            <div class="row my-2">
              <div class="col-12 text-lg text-center">
                @foreach($text_material->curriculums as $curriculum)
                <span class="text-sm ml-1">
                  <small class="badge badge-primary mt-1 mr-1">
                    {{$curriculum->name}}
                  </small>
                </span>
                @endforeach
              </div>
            </div>
            {{--
            <div class="row my-2">
              {!!nl2br($text_material->description)!!}
            </div>
            --}}
            <div class="row my-2">
              <div class="col-12">
                <a href="{{$text_material->s3_url}}" target="_blank" role="button" class="btn btn-info btn-sm float-left mr-1">
                  <i class="fa fa-cloud-download-alt"></i>
                </a>
                @if($text_material->is_publiced()==false)
                <a href="javascript:void(0);" page_title="{{__('labels.text_materials')}}{{__('labels.share')}}" page_form="dialog" page_url="/text_materials/{{$text_material->id}}/shared?origin={{$domain}}&item_id={{$item->id}}" role="button" class="btn
                  @if($text_material->shared_users()->count()>0)
                  btn-warning
                  @else
                  btn-secondary
                  @endif
                  btn-sm float-left mr-1">
                  <i class="fa fa-share-alt-square"></i>
                </a>
                @endif

                <a href="javascript:void(0);" page_title="{{__('labels.text_materials')}}{{__('labels.edit')}}" page_form="dialog" page_url="/text_materials/{{$text_material->id}}/edit?origin={{$domain}}&item_id={{$item->id}}" role="button" class="btn btn-default btn-sm float-right mr-1">
                  <i class="fa fa-edit"></i>
                </a>
                <a href="javascript:void(0);" page_title="{{__('labels.text_materials')}}{{__('labels.delete')}}" page_form="dialog" page_url="/text_materials/{{$text_material->id}}?origin={{$domain}}&item_id={{$item->id}}&action=delete" role="button" class="btn btn-default btn-sm float-right mr-1">
                  <i class="fa fa-trash"></i>
                </a>
              </div>
            </div>
            <div class="row my-2">
              <div class="col-12">
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
  <input type="hidden" name="view" value="{{$view}}">
  @component('tasks.components.search_subjects', ['_edit' => false, 'subjects' => $subjects, 'domain' => $domain,  'item' => (isset($item) ? $item : null)]) @endcomponent

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
        <input type="checkbox" value="1" name="is_publiced_only" class="icheck flat-green"
        @if(isset($filter['comment_filter']['is_publiced_only']) && $filter['comment_filter']['is_publiced_only']==1)
        checked
        @endif
        >{{__('labels.public')}}
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
