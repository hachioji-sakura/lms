<div id="{{$domain}}_create">
  <form id="edit" method="POST" action="/icon/change" enctype="multipart/form-data">
    @method('PUT')
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    <div class="row">
      <div class="col-12 mb-4">
        アイコン：
        <select name="change_icon" class="form-control" placeholder="アバター" >
          <option value="">{{__('labels.selectable')}}</option>
          @if(isset($use_icons))
            @foreach($use_icons as $use_icon)
               <option value="{{ $use_icon->id }}">{{$use_icon->alias}}</option>
            @endforeach
          @endif
        </select>
      </div>
      <div class="col-12 mb-4">
        アップロード：
        <input type="file" name="image" class="form-control{{ $errors->has('image') ? ' is-invalid' : '' }}" placeholder="ファイル">
        @if ($errors->has('image'))
        <span class="invalid-feedback">
        <strong>{{ $errors->first('image') }}</strong>
        </span>
        @endif
      </div>
    </div>
    <div class="row">
      <div class="col-12 col-lg-6 col-md-6 mb-1">
          <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="{{$domain}}_create">
              設定する
          </button>
      </div>
      <div class="col-12 col-lg-6 col-md-6 mb-1">
          <button type="reset" class="btn btn-secondary btn-block">
              キャンセル
          </button>
      </div>
    </div>
  </form>
</div>
