@section('tasks')
<div class="card">
  <div class="card-header ui-sortable-handle">
    <h3 id="tasks" class="card-title">
      <i class="ion ion-clipboard mr-1"></i>
      タスク
    </h3>
    <div class="card-tools">
      <ul class="pagination pagination-sm">
        <li class="page-item">
          <a href="#" class="page-link">&#171;</a>
        </li>
        <li class="page-item">
          <a href="#" class="page-link">1</a>
        </li>
        <li class="page-item">
          <a href="#" class="page-link">2</a>
        </li>
        <li class="page-item">
          <a href="#" class="page-link">3</a>
        </li>
        <li class="page-item">
          <a href="#" class="page-link">&#187;</a>
        </li>
      </ul>
    </div>
  </div>
  <!-- /.card-header -->
  <div class="card-body p-0">
    <ul class="todo-list ui-sortable">
      <li>
        <div class="row">
          <div class="col-12">
            <small class="badge badge-danger">
              <i class="fa fa-clock mr-1"></i>当日
            </small>
            <span class="text">タスク概要タスク概要タスク概要タスク概要...</span>
          </div>
        </div>
        <div class="row my-1 pt-1">
          <div class="col-6">
            <i class="fa fa-calendar mr-1"></i>2018/11/5 10:52
          </div>
        </div>
      </li>
      <li>
        <div class="row">
          <div class="col-12">
            <small class="badge badge-danger">
              <i class="fa fa-clock mr-1"></i>当日
            </small>
            <span class="text">タスク概要タスク概要タスク概要タスク概要...</span>
          </div>
        </div>
        <div class="row my-1 pt-1">
          <div class="col-6">
            <i class="fa fa-calendar mr-1"></i>2018/11/5 10:52
          </div>
        </div>
      </li>
      <li>
        <div class="row">
          <div class="col-12">
            <small class="badge badge-danger">
              <i class="fa fa-clock mr-1"></i>当日
            </small>
            <span class="text">タスク概要タスク概要タスク概要タスク概要...</span>
          </div>
        </div>
        <div class="row my-1 pt-1">
          <div class="col-6">
            <i class="fa fa-calendar mr-1"></i>2018/11/5 10:52
          </div>
        </div>
      </li>
      <li>
        <div class="row">
          <div class="col-12">
            <small class="badge badge-warning">
              <i class="fa fa-clock mr-1"></i>今週
            </small>
            <span class="text">タスク概要タスク概要タスク概要タスク概要...</span>
          </div>
        </div>
        <div class="row my-1 pt-1">
          <div class="col-6">
            <i class="fa fa-calendar mr-1"></i>2018/11/6 10:52
          </div>
        </div>
      </li>
      <li>
        <div class="row">
          <div class="col-12">
            <small class="badge badge-success">
              <i class="fa fa-clock mr-1"></i>今月
            </small>
            <span class="text">タスク概要タスク概要タスク概要タスク概要...</span>
          </div>
        </div>
        <div class="row my-1 pt-1">
          <div class="col-6">
            <i class="fa fa-calendar mr-1"></i>2018/11/12 10:52
          </div>
        </div>
      </li>
    </ul>
    <div class="row">
      <div class="col-12">
        <button type="button" class="btn btn-default w-100">
          一覧を見る
        </button>
      </div>
    </div>
  </div>
  <!-- /.card-body -->
  <!--
  <div class="card-footer clearfix">
    <div class="row">
      <div class="col-12">
        <button type="button" class="btn btn-info float-right">
          <i class="fa fa-plus mr-1"></i>追加
        </button>
      </div>
    </div>
  </div>
-->
</div>
@endsection
