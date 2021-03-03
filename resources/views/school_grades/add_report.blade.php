<div class="col-12">
  <div class="row mb-2">
    <div class="col-6">
      <select name="subject" width="100%" class="form-control select2">
        <option value=" ">{{__('labels.selectable')}}</option>
        @foreach($subjects as $key => $name)
        <option value="{{$key}}" {{$_edit == true &&  isset($report) && $report->subject_id == $key ? "selected" : ''}}>{{$name}}</option>
        @endforeach
      </select>
    </div>
    <div class="col-3 text-center">
      <select name="report_point" width="100%" class="form-control">
      @if(isset($report_type_point))
        @foreach($report_type_point as $key => $value)
          <option value="{{$key}}" {{$_edit == true && isset($report) &&  $report->report_point == $key ? "selected" : ''}} >{{$value}}</option>
        @endforeach
      @endif
      </select>
    </div>
    <div class="col-3">
      <button class="btn btn-primary add" type="button"><i class="fa fa-plus"></i>{{__('labels.add')}}</button>
    </div>
  </div>
</div>

<div class="col-12 col-md-12 mb-1">
  <div class="row reports">
    @if($_edit == true)
      @foreach($item->school_grade_reports as $report)
      <div class="col-6">
        <label>{{$report->subject_name}}:{{$report->report_point_name}}</label>
        <input type="hidden" name="reports[{{$report->subject_id}}]" value="{{$report->report_point}}">
        <button type="button" class="btn btn-default btn-sm float-right delete"><i class="fa fa-times"></i></button>
      </div>
      @endforeach
    @endif
  </div>
</div>
