@section('page_footer_form')
<div id="comment_add" class="card card-primary card-outline collapse">
  <form id="edit" method="POST" action="/@yield('domain')/{{$item->id}}/comments/create">
    @csrf
    <div class="card-header">
      <h3 class="card-title">コメント追加</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
      <div class="row form-group">
        <div class="col-12">
          <select name="type" class="form-control" placeholder="コメント種別" required="true">
            @foreach(config('attribute.comment_type') as $index => $name)
               <option value="{{ $index }}" @if(old('type') == $index) selected @endif>{{$name}}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="row form-group">
        <div class="col-12">
          <input name="title" class="form-control" placeholder="タイトル50文字まで" maxlength=50 required="true" value="{{old('title')}}">
        </div>
      </div>
      <div class="row form-group">
        <div class="col-12">
          <textarea name="body" id="compose-textarea" class="form-control" style="height: 80px" placeholder="内容2000文字まで" maxlength=2000 required="true">{{old('body')}}</textarea>
        </div>
      </div>
      {{--
      <div class="form-group">
        <div class="btn btn-default btn-file">
          <i class="fa fa-paperclip"></i>Attachment
          <input type="file" name="attachment">
        </div>
        <p class="help-block">Max. 32MB</p>
      </div>
      --}}
    </div>
    <!-- /.card-body -->
    <div class="card-footer">
      <button type="submit" class="btn btn-primary" accesskey="comment_add">
        <i class="fa fa-envelope mr-2"></i>送信
      </button>
      <button type="button" class="btn btn-secondary" onClick="$('#comment_add').collapse('hide');">
        <i class="fa fa-times mr-2"></i>キャンセル
      </button>
    </div>
    <!-- /.card-footer -->
  </form>
</div>
<script>
$(function(){
  @if((env('APP_DEBUG')))
  var _data = [
    { "title" : "数学がわからない",
      "body" : "先日のマンツーマンでの\n授業内容で定理の説明が理解できていないです",
      "type" : "study",
    },
    { "title" : "学校の宿題が多すぎて提出できない",
      "body" : "英語の宿題が多く\n来週の提出ですが間に合わないです。",
      "type" : "study",
    },
    { "title" : "内部進学が厳しい",
      "body" : "4科目の内申点が低く\n内部進学が厳しいといわれてしまいました",
      "type" : "promotion",
    },
    { "title" : "模試の結果がC判定で、志望校の見直しをしたほうがよいかどうか",
      "body" : "模試の結果より、日大を志望していましたがC判定だったため、\n次回来塾の際にご相談したいです",
      "type" : "promotion",
    },
    { "title" : "テキスト購入のご相談",
      "body" : "パーフェクト英単語を買って、\n学習しようと考えています",
      "type" : "other",
    },
    { "title" : "次回水曜日の授業をお休みにしたいです",
      "body" : "お休み後、振替えについてどのようにすればよろしいでしょうか？",
      "type" : "other",
    }
  ];
  var data = _data[(Math.random()*100|0) % _data.length];
  base.pageSettinged("comment_add", data);
  @else
  base.pageSettinged("comment_add", null);
  @endif
  $(".btn[type=submit][accesskey]").on("click", function(e){
    var _accesskey = $(this).attr("accesskey");
		console.log(_accesskey+": btn.submit");
		if(!front.validateFormValue(_accesskey)) return false;
    $("#"+_accesskey+" form").submit();
	});

  $("footer.main-footer a.btn[accesskey], a.nav-link[accesskey]").on("click", function(e){
    var _accesskey = $(this).attr("accesskey");
    switch(_accesskey){
      case "comment_add":
        $('#'+_accesskey).collapse('show');
        break;
      case "task_add":
        break;
      case "milestone_add":
        break;
    }
  });

});
</script>
@endsection

@section('page_footer')
<dt>
  <a class="btn btn-app" href="javascript:void(0);" accesskey="comment_add">
    <i class="fa fa-comment-dots"></i>コメント登録
  </a>
</dt>
{{-- まだ対応しない
  <dt>
    <a class="btn btn-app" href="javascript:void(0);" accesskey="task_add" disabled>
      <i class="fa fa-plus"></i>タスク登録
    </a>
  </dt>
  <dt>
    <a class="btn btn-app" href="javascript:void(0);" accesskey="milestone_add" disabled>
      <i class="fa fa-flag"></i>目標登録
    </a>
  </dt>
--}}
@endsection
