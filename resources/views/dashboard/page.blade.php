@section('title')
@if($_del)
  @yield('domain_name')削除
@else
  @yield('domain_name')詳細
@endif
@endsection

@extends('dashboard.common')

@section('contents')
<div class="card-header">
  <h3 class="card-title">@yield('title')</h3>
  @if($_del)
  <h6>以下の項目を削除してもよろしいですか？</h6>
  @endif
</div>
<div class="card-body">
    @foreach($fields as $key=>$field)
      <div class="row">
        <div class="col-12 col-lg-6 col-md-6">
          <div class="form-group">
            <label for="{{$key}}" class="w-100">
              {{$field['label']}}
            </label>
            {{$item[$key]}}
          </div>
        </div>
      </div>
    @endforeach
      <form id="edit" method="POST" action="/@yield('domain')/{{$item['id']}}">
        @csrf
      <div class="row">
        @if($_del)
          @method('DELETE')
          <div class="col-12 col-lg-6 col-md-6 mb-1">
              <button type="submit" class="btn btn-danger btn-block" accesskey="delete">
                  削除する
              </button>
          </div>
        @endif
        <div class="col-12 col-lg-6 col-md-6 mb-1">
            <button type="button" class="btn btn-secondary btn-block" accesskey="cancel" onClick="history.back();">
                戻る
            </button>
        </div>
      </div>
      </form>
    </div>
</div>
@yield('scripts')
@endsection
