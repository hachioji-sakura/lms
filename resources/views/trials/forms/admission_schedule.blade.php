<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-yen-sign mr-1"></i>
    受講料につきまして
  </div>
  <div class="col-6 p-3 font-weight-bold" >通塾回数</div>
  <div class="col-6 p-3">
    週{{count($item->calendar_settings)}}回
  </div>
  <div class="col-6 p-3 font-weight-bold" >月会費</div>
  <div class="col-6 p-3">
    1,500円（税抜き）
  </div>
  <div class="col-6 p-3 font-weight-bold" >入会金</div>
  <div class="col-6 p-3">
    15,000円（税抜き）
  </div>
</div>
@if(count($item->calendar_settings)>0)
  <table class="table w-100">
    <tr class="bg-gray">
      <th class="border-right">
        曜日・時間帯
      </th>
      <th class="border-right">
        講師・内容
      </th>
      <th>受講料</th>
    </tr>
  @foreach($item->calendar_settings as $setting)
  <tr class="bg-light">
    <td class="border-right">
      {{$setting["lesson_week_name"]}}曜日<br>
      {{$setting->timezone()}}
    </td>
    <td class="border-right">
      @foreach($setting['teachers'] as $member)
        {{$member->user->teacher->name()}}
      @endforeach
      <br>
      {{$setting->course()}}
      @foreach($setting->subject() as $subject)
      <span class="text-xs mx-2">
        <small class="badge badge-primary mt-1 mr-1">
          {{$subject}}
        </small>
      </span>
      @endforeach
    </td>
    <td>
      〇〇円 / 時間
    </td>
  </tr>
  @endforeach
  </table>
@else
<div class="row">
  <div class="col-12 bg-danger p-4 pl-4 mb-4">
    <i class="fa fa-exclamation-triangle mr-1"></i>
    授業予定が登録されていません
  </div>
</div>
@endif
