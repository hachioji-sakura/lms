<div class="col-12 col-md-6 mb-1 report_point">
  <div class="row">
    <div class="col-1">
      <button type="button" class="btn btn-tool delete float-right"><i class="fa fa-times-circle"></i></button>
    </div>
    <div class="col-8">
      <select name="subject[]" width="100%" class="form-control select2">
        <option value=" ">{{__('labels.selectable')}}</option>
        @foreach($subjects as $key => $name)
        <option value="{{$key}}" {{$_edit == true &&  isset($report) && $report->subject_id == $key ? "selected" : ''}}>{{$name}}</option>
        @endforeach
      </select>
    </div>
    <div class="col-3 text-center">
      <select name="report_point[]" width="100%" class="form-control">
        @for($i=1;$i<=10;$i++)
        <option value="{{$i}}" {{$_edit == true && isset($report) &&  $report->report_point == $i ? "selected" : ''}} >{{$i}}</option>
        @endfor
      </select>
    </div>
  </div>
</div>
