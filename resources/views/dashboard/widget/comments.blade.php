
@section('comments')
<!-- Custom Tabs -->
<div class="card">
  <div class="card-header d-flex p-0">
    <h3 id="comments" class="card-title p-2">
      <i class="fa fa-comments mr-1"></i>
      {{__('labels.comments')}}
    </h3>
    <br>
    <ul class="nav nav-pills ml-auto p-2">
      <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false">
          {{__('labels.comment_type')}} <span class="caret"></span>
        </a>
        <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 40px, 0px); top: 0px; left: 0px; will-change: transform;">
          <a class="dropdown-item" tabindex="all" href="#comments_tab_all" data-toggle="tab">すべて</a>
          @foreach($attributes['comment_type'] as $index => $name)
             <a class="dropdown-item" tabindex="{{ $index }}" href="#comments_tab_{{ $index }}" data-toggle="tab">{{$name}}</a>
           @endforeach
        </div>
      </li>
    </ul>
  </div>
  <!-- /.card-header -->
  <div class="card-body p-0">
    <div class="tab-content">
      @include('components.comments', [
        'comments'=>$comments,
        'comment_type'=> 'all',
        'is_active' => 'active'
        ])
      @include('components.comments', [
        'comments'=>$comments,
        'comment_type'=> 'study',
        'is_active' => ''
        ])
        @include('components.comments', [
          'comments'=>$comments,
          'comment_type'=> 'promotion',
          'is_active' => ''
          ])
        @include('components.comments', [
          'comments'=>$comments,
          'comment_type'=> 'other',
          'is_active' => ''
          ])
    </div>
  </div>
</div>
@endsection
