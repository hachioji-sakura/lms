<div class="card card-widget widget-user-2">
  <!-- Add the bg color to the header using any of the bg-* classes -->
  <div class="widget-user-header bg-light" >
    <a class="widget-user-image" href="javascirpt:void(0);" accesskey="icon_change">
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
    @if($item->user_id == $user->user_id)
    <div id="icon_change" class="card card-outline collapse">
      <form method="POST" action="/{{$domain}}/{{$item->id}}/icon" enctype="multipart/form-data">
        @csrf
        <!-- /.card-header -->
        <div class="card-body">
          <div class="row form-group">
            <div class="col-12">
              アイコン：
              <select name="change_icon" class="form-control" placeholder="アバター" >
                <option value="">選択してください</option>
                @if(isset($use_icons))
                  @foreach($use_icons as $use_icon)
                     <option value="{{ $use_icon->id }}">{{$use_icon->alias}}</option>
                  @endforeach
                @endif
              </select>
            </div>
          </div>
          <div class="row form-group">
            <div class="col-12">
              アップロード：
              <input type="file" name="image" class="form-control{{ $errors->has('image') ? ' is-invalid' : '' }}" placeholder="ファイル">
              @if ($errors->has('image'))
              <span class="invalid-feedback">
              <strong>{{ $errors->first('image') }}</strong>
              </span>
              @endif
            </div>
          </div>
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-primary" accesskey="icon_change">
            <i class="fa fa-save mr-2"></i><span class="btn-label">変更</span>
          </button>
          <button type="reset" class="btn btn-secondary" onClick="$('#icon_change').collapse('hide');">
            <i class="fa fa-times mr-2"></i><span class="btn-label">キャンセル</span>
          </button>
        </div>
      </form>
    </div>
    @endif
  </div>
</div>
<!-- /.widget-user -->
<script>

$(function(){
  base.pageSettinged("icon_change", data);
  $("a.widget-user-image[accesskey]").on("click", function(e){
    var _accesskey = $(this).attr("accesskey");
    switch(_accesskey){
      case "icon_change":
        $('#'+_accesskey).collapse('toggle');
        break;
    }
  });

});
</script>
