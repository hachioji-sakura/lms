@section('events')
<!-- Custom Tabs -->
<div class="card">
  <div class="card-header d-flex p-0">
    <h3 id="events" class="card-title p-2">
      <i class="fa fa-calendar-alt mr-1"></i>
      イベント
    </h3>
    <br>
    <ul class="nav nav-pills ml-auto p-2">
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false">
          イベント種別 <span class="caret"></span>
        </a>
        <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 40px, 0px); top: 0px; left: 0px; will-change: transform;">
          <a class="dropdown-item" tabindex="1" href="#events_tab_1" data-toggle="tab">学校</a>
          <a class="dropdown-item" tabindex="2" href="#events_tab_2" data-toggle="tab">試験</a>
          <a class="dropdown-item" tabindex="3" href="#events_tab_3" data-toggle="tab">その他</a>
        </div>
      </li>
      <!--
      <li class="nav-item">
        <a class="nav-link active" href="#events_tab_1" data-toggle="tab">学校</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#events_tab_2" data-toggle="tab">試験</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#events_tab_3" data-toggle="tab">その他</a>
      </li>
    -->
    </ul>
  </div>
  <!-- /.card-header -->
  <div class="card-body p-0">
    <div class="tab-content">
      <div class="tab-pane active" id="events_tab_1">
<!-- tab1 start-->
<div class="card card-widget">
<!-- /.card-header -->
<!-- /.card-body -->
<div class="card-comments">
<div class="card-comment">
<!-- User image -->
<img class="img-sm mr-1" src="../img/school/note.png" alt="User Image">
<span class="username">宿題提出
<span class="text-muted float-right">2018/11/1 20:21</span>
</span>
<div class="comment-text">
イベント内容イベント内容イベント内容イベント内容イベント内容イベント内容
</div>
<!-- /.comment-text -->
</div>
<!-- /.card-comment -->
<div class="card-comment">
<!-- User image -->
<img class="img-sm" src="../img/school/calendar2.png" alt="User Image">
<div class="comment-text">
<span class="username">2学期中間試験
<span class="text-muted float-right">2018/11/1 21:33</span>
</span>
イベント内容イベント内容イベント内容イベント内容イベント内容イベント内容
</div>
<!-- /.comment-text -->
</div>
<!-- /.card-comment -->
<!-- /.card-comment -->
<div class="card-comment">
<!-- User image -->
<img class="img-sm" src="../img/school/discussion.png" alt="User Image">
<div class="comment-text">
<span class="username">進路相談
<span class="text-muted float-right">2018/11/1 21:33</span>
</span>
イベント内容イベント内容イベント内容イベント内容イベント内容イベント内容
</div>
<!-- /.comment-text -->
</div>
<!-- /.card-comment -->
</div>
<div class="row">
<div class="col-12">
<button type="button" class="btn btn-default w-100">
一覧を見る
</button>
</div>
</div>
</div>
<!-- tab1 end -->
      </div>
      <!-- /.tab-pane -->
      <div class="tab-pane" id="events_tab_2">
<!-- tab2 start-->
<div class="card card-widget">
<!-- /.card-header -->
<!-- /.card-body -->
<div class="card-comments">
<div class="card-comment">
<!-- User image -->
<img class="img-circle img-sm mr-1" src="../img/school/man.png" alt="User Image">
<span class="username">鈴木　一郎
<span class="text-muted float-right">2018/11/1 20:21</span>
</span>
<div class="comment-text">
進学・進級・受験に関するコメント
</div>
<!-- /.comment-text -->
</div>
<!-- /.card-comment -->
<div class="card-comment">
<!-- User image -->
<img class="img-circle img-sm" src="../img/school/sakura.png" alt="User Image">
<div class="comment-text">
<span class="username">SAKURAアカデミー
<span class="text-muted float-right">2018/11/1 21:33</span>
</span>
進学・進級・受験に関するコメント
</div>
<!-- /.comment-text -->
</div>
<!-- /.card-comment -->
</div>
<div class="row">
<div class="col-12">
<button type="button" class="btn btn-default w-100">
一覧を見る
</button>
</div>
</div>
</div>
<!-- tab2 end -->
      </div>
      <!-- /.tab-pane -->
      <div class="tab-pane" id="events_tab_3">
<!-- tab3 start -->
<div class="card card-widget">
<!-- /.card-header -->
<!-- /.card-body -->
<div class="card-comments">
<div class="card-comment">
<!-- User image -->
<img class="img-circle img-sm mr-1" src="../img/school/man.png" alt="User Image">
<span class="username">鈴木　一郎
<span class="text-muted float-right">2018/11/1 20:21</span>
</span>
<div class="comment-text">
その他のコメント
</div>
<!-- /.comment-text -->
</div>
<!-- /.card-comment -->
<div class="card-comment">
<!-- User image -->
<img class="img-circle img-sm" src="../img/school/sakura.png" alt="User Image">
<div class="comment-text">
<span class="username">SAKURAアカデミー
<span class="text-muted float-right">2018/11/1 21:33</span>
</span>
コメント内容
コメント内容
コメント内容
コメント内容
コメント内容
</div>
<!-- /.comment-text -->
</div>
<!-- /.card-comment -->
</div>
<div class="row">
<div class="col-12">
<button type="button" class="btn btn-default w-100">
一覧を見る
</button>
</div>
</div>
</div>
<!-- tab3 end -->
      </div>
      <!-- /.tab-pane -->
    </div>
    <!-- /.tab-content -->
  </div>
  <!-- /.card-body -->
</div>
<!-- ./card -->
@endsection
