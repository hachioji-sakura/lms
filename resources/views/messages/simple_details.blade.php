<div class="row">
  <div class="col-12">
    <label for="create_user" class="w-100 bg-success">
      <i class="fas fa-user ml-1 mr-1"></i>{{__('labels.from')}}
    </label>
    {{$item->create_user->details()->name()}}
  </div>
</div>
<div class="row mt-3">
  <div class="col-12">
    <label for="body" class="w-100 bg-success">
      <i class="fas fa-file-alt  ml-1 mr-1"></i>{{__('labels.body')}}
    </label>
  </div>
</div>
<div class="row">
  <div class="col-12 ">
    {!! nl2br($item->body) !!}
  </div>
</div>
@if( !empty($item->s3_url) )
<div class="row mt-3">
  <div class="col-12">
    <label for="body">
      <span class="mr-1">
        <a href="{{$item->s3_url}}" target="_blank">
          <i class="fa fa-link mr-1"></i>
          {{$item->s3_alias}}
        </a>
      </span>
    </label>
  </div>
</div>
@endif

<div class="row mt-3">
  <div class="col-12">
    <button type="reset" class="btn btn-sm btn-secondary btn-block">
      {{__('labels.close_button')}}
    </button>
  </div>
</div>
