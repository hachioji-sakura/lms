@extends('layouts.loginbox')
@section('title')
{{$item["type_name"]}}
@endsection

@section('title_header')
{{$item["type_name"]}}
@endsection
@section('content')
  @component('components.page', ['item' => $item, 'fields' => $fields, 'domain' => $domain, 'action' => ''])
    @slot('page_message')
      @if($item->status=="new")
      <div class="col-12 bg-danger p-2 mb-2">
        <i class="fa fa-exclamation-triangle mr-1"></i>    {{__('messages.confirm_ask_data')}}
      </div>
      @endif
    @endslot

    @slot('forms')
    @if($item->status=='new')
    <div class="row">
    <div class="col-12 col-md-6 mb-1" id="commit_form">
      <form method="POST" action="/asks/{{$item['id']}}/status_update/commit">
        @csrf
        <input type="text" name="dummy" style="display:none;" / >
        @method('PUT')
        <button type="button" class="btn btn-submit btn-success btn-block"  accesskey="commit_form" confirm="{{__('messages.confirm_update')}}">
          <i class="fa fa-check mr-1"></i>
          {{__('labels.approval')}}
        </button>
      </form>
    </div>
    <div class="col-12 col-md-6 mb-1" id="cancel_form">
      <form method="POST" action="/asks/{{$item['id']}}/status_update/cancel">
        @csrf
        <input type="text" name="dummy" style="display:none;" / >
        @method('PUT')
        <button type="button" class="btn btn-submit btn-danger btn-block"  accesskey="cancel_form" confirm="{{__('messages.confirm_update')}}">
          <i class="fa fa-times mr-1"></i>
          差戻
        </button>
      </form>
    </div>
    @endif
    <script>
    $(function(){

      //submit
      $("button.btn-submit[accesskey=commit_form]").on('click', function(e){
        e.preventDefault();
        if(front.validateFormValue('commit_form')){
          $(this).prop("disabled",true);
          $("#commit_form form").submit();
        }
      });

      $("button.btn-submit[accesskey=cancel_form]").on('click', function(e){
        e.preventDefault();
        if(front.validateFormValue('commit_form')){
          $(this).prop("disabled",true);
          $("#cancel_form form").submit();
        }
      });
    });

    </script>
    @endslot
  @endcomponent
@endsection
