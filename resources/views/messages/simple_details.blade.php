<div class="row">
  <div class="col-12">
    <label for="title" class="w-100">
      {{__('labels.title')}}
    </label>
    {{$item->title}}
  </div>
</div>
<div class="row mt-3">
  <div class="col-6">
    <label for="create_user" class="w-100">
      {{__('labels.from')}}
    </label>
    {{$item->create_user->details()->name()}}
  </div>
</div>
<div class="row mt-3">
  <div class="col-12">
    <label for="body" class="w-100">
      {{__('labels.body')}}
    </label>
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
