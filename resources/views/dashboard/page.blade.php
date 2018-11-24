@section('title')
  @yield('domain_name')詳細
@endsection
@extends('dashboard.common')
@include('dashboard.menu.page_sidemenu')

@section('contents')
<div class="card-header">
  <h3 class="card-title">@yield('title')</h3>
</div>
<div class="card-body">
    @foreach($fields as $key=>$field)
      <div class="row">
        <div class="col-12 col-lg-6 col-md-6">
          <div class="form-group">
            <label for="{{$key}}">
              {{$field['label']}}
            </label>
            {{$item[$key]}}
          </div>
        </div>
      </div>
    @endforeach
      <div class="row">
        <div class="col-12>
            <button type="button" class="btn btn-secondary btn-block" accesskey="cancel" onClick="history.back();">
                戻る
            </button>
        </div>
      </div>
    </div>
</div>
@yield('scripts')
@endsection
