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
            <a href="{{url()->previous()}}">
              <i class="fa fa-arrow-left mr-2"></i>
            </a>
            {{$item['title']}}
          </h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          {!!nl2br($item['body'])!!}
        </div>
      </div>
    </div>
  </div>
</section>

@endsection
@section('page_footer')
@if(isset($user) && $user->role=='manager')
{{--
<dt>
  <a class="btn btn-app"  href="javascript:void(0);" page_title="{{$domain_name}}{{__('labels.edit')}}" page_form="dialog" page_url="/{{$domain}}/{{$item['id']}}/edit" role="button" >
    <i class="fa fa-edit"></i>{{__('labels.edit')}}
  </a>
</dt>
  <dt>
    <a class="btn btn-app"  href="javascript:void(0);" page_title="{{$domain_name}}{{__('labels.delete')}}" page_form="dialog" page_url="/{{$domain}}/{{$item['id']}}?action=delete" role="button">
      <i class="fa fa-times"></i>{{__('labels.delete')}}
    </a>
  </dt>
  --}}
@endif
@endsection
