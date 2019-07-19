@section('title')
{{__('labels.faqs')}}{{__('labels.list')}}
@endsection
@extends('dashboard.common')
@extends('faqs.menu')

@section('contents')
<section class="content">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">
            <i class="fa fa-question-circle mr-1" ></i>
            @yield('title')
          </h3>
          @component('components.search_word', ['search_word' => $search_word])
          @endcomponent
        </div>
        <!-- /.card-header -->
        <div class="card-body table-responsive p-0">
          @if(count($items) > 0)
          <ul class="mailbox-attachments clearfix row">
            @foreach($items as $item)
            <li class="col-12" accesskey="" target="">
              <div class="row">
                <div class="col-12 mt-1">
                  <a href="/{{$domain}}/{{$item["id"]}}/page" >
                    <i class="fa fa-info-circle mx-1"></i>{{$item->title}}<br>
                  </a>
                </div>
                <div class="col-6 mt-1 text-sm">
                  {{__('labels.publiced_at')}}： {{date('Y-m-d', strtotime($item["publiced_at"]))}}
                </div>
                @if(isset($user) && $user->role=='manager')
                <div class="col-12 text-sm mt-1 text-right">
                  <a class="btn btn-sm btn-success"  href="javascript:void(0);" page_title="{{$domain_name}}{{__('labels.edit')}}" page_form="dialog" page_url="/{{$domain}}/{{$item['id']}}/edit" role="button" >
                    <i class="fa fa-edit mr-1"></i>{{__('labels.edit')}}
                  </a>
                  <a class="btn btn-sm btn-danger"  href="javascript:void(0);" page_title="{{$domain_name}}{{__('labels.delete')}}" page_form="dialog" page_url="/{{$domain}}/{{$item['id']}}?action=delete" role="button">
                    <i class="fa fa-times mr-1"></i>{{__('labels.delete')}}
                  </a>
                </div>
                @endif
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


@endsection
