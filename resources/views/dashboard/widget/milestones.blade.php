@section('milestones')
<div class="card">
  <div class="card-header">
    <h3 class="card-title">
      <i class="fa fa-flag mr-1"></i>目標
    </h3>
  </div>
  <!-- /.card-header -->
  <div class="card-body">
    <div id="accordion">
      <!-- we are adding the .class so bootstrap.js collapse plugin detects it -->
      <div class="card card-outline card-primary">
        <div class="card-header">
          <h4 class="card-title">
            <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" class="" aria-expanded="true">
              目標1.タイトル：・・・・
            </a>
          </h4>
        </div>
        <div id="collapseOne" class="panel-collapse in collapse show" style="">
          <div class="card-body">
            目標詳細：・・・・・・・・・・・・・・・・・・・
          </div>
        </div>
      </div>
      <div class="card card-outline card-danger">
        <div class="card-header">
          <h4 class="card-title">
            <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" class="collapsed" aria-expanded="false">
              目標2.タイトル：・・・・
            </a>
          </h4>
        </div>
        <div id="collapseTwo" class="panel-collapse collapse">
          <div class="card-body">
            目標詳細：・・・・・・・・・・・・・・・・・・・
          </div>
        </div>
      </div>
      <div class="card card-outline card-success">
        <div class="card-header">
          <h4 class="card-title">
            <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree" class="collapsed" aria-expanded="false">
              目標3.タイトル：・・・・
            </a>
          </h4>
        </div>
        <div id="collapseThree" class="panel-collapse collapse">
          <div class="card-body">
            目標詳細：・・・・・・・・・・・・・・・・・・・
          </div>
        </div>
      </div>
    </div>
    <!-- /.card-body -->
  </div>
</div>
@endsection
