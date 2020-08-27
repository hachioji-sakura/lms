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
            @if(isset($_edit) && $_edit==true)
            FAQ登録
            @else
            FAQ更新
            @endif
          </h3>
        </div>
        <div id="{{$domain}}_create" class="card-body">
          @if(isset($_edit) && $_edit==true)
          <form id="edit" method="POST" action="/{{$domain}}/{{$item['id']}}">
            @method('PUT')
          @else
          <form id="edit" method="POST" action="/{{$domain}}">
          @endif
          @csrf
          <input type="text" name="dummy" style="display:none;" / >
            <div class="row">
              <div class="col-12">
                <div class="form-group">
                  <label for="title" class="w-100">
                    種別
                    <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
                  </label>
                  <select name="type" class="form-control" placeholder="種別" required="true">
                    @foreach($attributes['faq_type'] as $index => $name)
                       <option value="{{ $index }}"
                       @if(isset($_edit)  && $_edit==true && $item['type'] == $index)
                       selected
                       @endif
                       >{{$name}}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <div class="form-group">
                  <label for="title" class="w-100">
                    タイトル
                    <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
                  </label>
                  <input type="text" id="title" name="title" class="form-control" required="true" maxlength=50
                  @if(isset($_edit) && $_edit==true)
                   value="{{$item['title']}}" placeholder="(変更前) {{$item['title']}}">
                  @else
                   placeholder="50文字まで">
                  @endif
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <div class="form-group">
                  <label for="body" class="w-100">
                    内容
                    <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
                  </label>
                  <textarea type="text" id="body" name="body" class="" rows=5 required="true"  maxlength=5000 uitype="ckeditor"
                  @if(isset($_edit) && $_edit==true)
                    placeholder="(変更前) {{$item['body']}}" >{{$item['body']}}</textarea>
                  @else
                    placeholder="5000文字まで" ></textarea>
                  @endif
                </div>
              </div>
              <div class="col-6">
                <div class="form-group">
                  <label for="publiced_at" class="w-100">
                    {{__('labels.sort_no')}}
                    <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
                  </label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                    </div>
                    <input type="text" name="sort_no" class="form-control float-left" inputtype="number"
                    @if(isset($_edit) && $_edit==true)
                      value="{{$item['sort_no']}}"
                    @endif
                    @if(!(isset($_edit) && $_edit==true))
                    minvalue="1"
                    @endif
                    required="true" >
                  </div>
                </div>
              </div>
              <div class="col-6">
                <div class="form-group">
                  <label for="publiced_at" class="w-100">
                    {{__('labels.publiced_at')}}
                    <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
                  </label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                    </div>
                    <input type="text" id="publiced_at" name="publiced_at" class="form-control float-left" required="true" uitype="datepicker" placeholder="例：{{date('Y/m/d')}}"
                    @if(isset($_edit) && $_edit==true && isset($item) && isset($item['publiced_at']) && $item['publiced_at']!='9999-12-31')
                      value="{{date('Y/m/d', strtotime($item['publiced_at']))}}"
                    @elseif(isset($item) && isset($item['publiced_at']) && $item['publiced_at']!='9999-12-31')
                      value="{{date('Y/m/d', strtotime($item['publiced_at']))}}"
                    @else
                      value = "{{date('Y/m/d')}}"
                    @endif
                    @if(!(isset($_edit) && $_edit==true))
                    minvalue="{{date('Y/m/d')}}"
                    @endif
                    >
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-12 col-md-6 mb-1">
                  <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="{{$domain}}_create">
                    @if(isset($_edit) && $_edit==true)
                      {{__('labels.update_button')}}
                    @else
                      登録する
                    @endif
                  </button>
                  @if(isset($error_message))
                    <span class="invalid-feedback d-block ml-2 " role="alert">
                        <strong>{{$error_message}}</strong>
                    </span>
                  @endif
              </div>
              <div class="col-12 col-md-6 mb-1">
                  <a href="/{{$domain}}" class="btn btn-secondary btn-block">
                      戻る
                  </a>
              </div>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</section>
<script>
$(function(){
  base.pageSettinged("{{$domain}}_create", null);
  //submit
  $("button.btn-submit").on('click', function(e){
    e.preventDefault();
    if(front.validateFormValue('{{$domain}}_create')){
      $(this).prop("disabled",true);
      $("form").submit();
    }
  });
});
</script>

@endsection
