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
              @component('text_materials.forms.buttons', ['text_material' => $text_material, 'item' => $item, 'domain' => $domain]) @endcomponent
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
  @component('text_materials.forms.filter', ['view' => $view, 'filter' => $filter, '_page' => $_page, '_line' => $_line, 'domain' => $domain, 'domain_name' => $domain_name, 'attributes'=>$attributes, 'subjects' => $subjects])
  @endcomponent
</div>
@endsection

@section('sub_contents')
@yield('text_materials')
@endsection
