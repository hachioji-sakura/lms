
{{--
@component('students.forms.agreement', ['item' => $student, 'fields' => $fields, 'domain' => $domain, 'input' => true,  'user'=>$user]) @endcomponent
--}}
<section id="agreement_confirm" class="content-header">
  <form method="POST" action="/agreements">
    @csrf
    <input type="hidden" name="agreements[student_id]" value="{{$item->id}}">
    <input type="hidden" name="agreements[student_parent_id]" value="{{$item->relations->first()->student_parent_id}}">
    <div class="card mb-2">
      <div class="card-header d-flex p-0">
      <h4 class="card-title p-3 text-sm">
        <i class="fa fa-file-signature"></i>
        基本契約内容
      </h4>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-6 p-2 font-weight-bold" >学年</div>
          <div class="col-6 p-2">{{$item->grade()}}</div>
          <div class="col-6 p-2 font-weight-bold" >入会金</div>
          <div class="col-6 p-2">
            {{number_format($agreement->entry_fee)}}円(税込み)
            <input type="hidden" name="agreements[entry_fee]" value="{{$item->get_entry_fee()}}">
          </div>
          <div class="col-6 p-2 font-weight-bold" >月会費</div>
          <div class="col-6 p-2">
            <input type="text" class="form-control w-50 float-left" name="agreements[monthly_fee]" value="{{$agreement->monthly_fee}}">
            <span class="ml-2 float-left mt-2">円</span>
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-2">
      <div class="card-header d-flex p-0">
        <h4 class="card-title p-3 text-sm">
          <i class="fa fa-user-clock"></i>
          通塾内容
        </h4>
      </div>
      <div class="card-body">
        <div class="row">
          @foreach($agreement->agreement_statements as $statement)
          <input type="hidden" name="agreement_statements[{{$statement->id}}][setting_key]" value="{{$statement->id}}">
          <input type="hidden" name="agreement_statements[{{$statement->id}}][lesson_id]" value="{{$statement->lesson_id}}">
          <input type="hidden" name="agreement_statements[{{$statement->id}}][teacher_id]" value="{{$statement->teacher_id}}">
          <input type="hidden" name="agreement_statements[{{$statement->id}}][course_type]" value="{{$statement->course_type}}">
          <input type="hidden" name="agreement_statements[{{$statement->id}}][course_minutes]" value="{{$statement->course_minutes}}">
          <input type="hidden" name="agreement_statements[{{$statement->id}}][lesson_week_count]" value="{{$statement->lesson_week_count}}">
          <input type="hidden" name="agreement_statements[{{$statement->id}}][lesson_id]" value="{{$statement->lesson_id}}">
          <input type="hidden" name="agreement_statements[{{$statement->id}}][grade]" value="{{$item->tag_value('grade')}}">
          <input type="hidden" name="agreement_statements[{{$statement->id}}][is_exam]" value="0">
          @foreach($statement->user_calendar_member_settings as $member)
          <input type="hidden" name="agreement_statements[{{$statement->id}}][user_calendar_member_setting_id][]" value="{{$member->id}}">
          @endforeach
          <div class="col-12 pl-2 bd-b bd-gray">
            <div class="row">
              <div class="col-12 p-2 font-weight-bold" >
                {{$statement->lesson_name}}/{{$statement->course_type_name}}
              </div>
              <div class="col-12 p-2 pl-4" >
                ・担当講師：{{$statement->teacher_name}}
              </div>
              <div class="col-12 p-2 pl-4" >
                ・授業時間：{{$statement->course_minutes_name}}
              </div>
              <div class="col-12 p-2 pl-4" >
                ・コマ数：{{$statement->lesson_week_count}}コマ/週
              </div>
              <div class="col-12 p-2 pl-4" >
                <div class="form-group">
                  <label for="tuition" class="w-100">
                    受講料
                    <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
                  </label>
                  <input type="text" name="agreement_statements[{{$statement->id}}][tuition]" class="form-control w-50 float-left" value="{{$statement->tuition}}"><span class="ml-2 float-left mt-2">円 / 時間</span>
                </div>
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-6 mb-1">
        <button type="button" class="btn btn-submit btn-primary btn-block"  accesskey="agreement_confirm">
          <i class="fa fa-sync mr-1"></i>
          {{__('labels.update_button')}}
        </a>
      </div>
      <div class="col-6 mb-1">
        <a href="javascript:void(0);" data-dismiss="modal" role="button" class="btn btn-secondary btn-block float-left mr-1">
          <i class="fa fa-times-circle mr-1"></i>
          {{__('labels.close_button')}}
        </a>
      </div>
    </div>
  </form>
</section>
