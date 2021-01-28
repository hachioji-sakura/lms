<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mt-4">
    <i class="fa fa-file-invoice mr-1"></i>
     勤務設定
  </div>
  <div class="col-12 p-2 font-weight-bold" >勤務可能校舎</div>
  <div class="col-12 pl-3">
    <span id="lesson_place_name">
    @if(isset($item)){{$item->get_tags_name('lesson_place')}}@endif
    </span>
  </div>
</div>
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mt-4">
    <i class="fa fa-clock mr-1"></i>
    ご希望の日時について
  </div>
  <div class="col-12 p-2 font-weight-bold ">
    勤務可能日時
  </div>
  <div class="col-12">
    <div class="form-group">
      <table class="table table-striped">
      <tr class="bg-gray">
        <th class="p-1 text-center ">日
        </th>
        <th class="p-1 text-center ">時間帯
        </th>
      </tr>
      <tbody id="hope_datetime_list">
        @if(isset($item))
          @foreach($item->request_dates as $request_date)
          <tr>
          <td class="bg-gray">{{$request_date->month_day}}</td>
          <td>{{$request_date->timezone}}</td>
          </tr>
          @endforeach
        @endif
      </tbody>
      </table>
    </div>
  </div>
</div>
