<div class="card card-widget widget-user-2">
  <!-- Add the bg color to the header using any of the bg-* classes -->
  <div class="widget-user-header bg-light" >
    @if($item->user_id == $user->user_id || $user->role==="parent" || $user->role==="manager")
    <a class="widget-user-image" page_form="dialog" page_url="/icon/change?origin={{$domain}}&item_id={{$item->id}}&user_id={{$item->user_id}}" page_title="アイコン変更">
    @else
    <a class="widget-user-image">
    @endif
      <img class="img-circle elevation-2" src="{{$item->icon}}" >
    </a>
    <h4 class="widget-user-username">
        <ruby style="ruby-overhang: none">
          <rb>{{$item->name}}</rb>
          <rt>{{$item->kana}}</rt>
          {{$courtesy}}
        </ruby>
    </h4>
    {{$alias}}
  </div>
</div>
