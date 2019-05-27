@if(count($item->get_access_member($user->user_id)) > 0)
  @foreach($item->get_access_member($user->user_id) as $member)
    @if($member->user->details("students")->role==="student")
      @if($item->is_group()==false && isset($student_id) && $student_id>0 && $member->user->details()->id!=$student_id)
        {{-- student_idが指定されている場合の対象は一人 --}}
        @continue
      @endif
      <input class="member_status" type="hidden" name="{{$member->id}}_status" value="{{$status}}">
    @endif
  @endforeach
@endif

{{--
  @if($is_hidden == true || count($item->get_access_member($user->user_id))==1)
  @else
  @if(count($item->get_access_member($user->user_id)) > 0)
    <div class="col-12 mb-1">
      <div class="form-group">
        <label for="status">
          個別連絡
          <span class="right badge badge-danger ml-1">必須</span>
        </label>
      </div>
    </div>
    <div class="col-12">
        <table class="table table-striped w-80" id="fix_list_table">
        <tr class="bg-gray">
          <th class="p-1 pl-2 text-sm "><i class="fa fa-user mr-1"></i>生徒氏名</th>
          <th class="p-1 pl-2 text-sm"><i class="fa fa-check mr-1"></i>ご連絡</th>
        </tr>
        @foreach($item->get_access_member($user->user_id) as $member)
        @if($member->user->details()->role==="student")
        <tr class="">
          <th class="p-1 pl-2">
            {{$member->user->details()->name}}</th>
          <td class="p-1 text-sm text-center">
            @if($member->status=="rest")
              <i class="fa fa-times mr-1"></i>お休み
            @else
            <div class="input-group">
              <div class="form-check">
                <input class="form-check-input icheck flat-green fix_check" type="radio" name="{{$member->id}}_status" id="{{$member->id}}_status_fix" value="fix" required="true" >
                <label class="form-check-label" for="{{$member->id}}_status_fix" validate="status_fix_check();">
                    出席予定
                </label>
              </div>
              <div class="form-check ml-2">
                <input class="form-check-input icheck flat-red fix_check" type="radio" name="{{$member->id}}_status" id="{{$member->id}}_status_cancel" value="cancel" required="true" >
                <label class="form-check-label" for="{{$member->id}}_status_cancel" validate="status_fix_check();">
                    キャンセル
                </label>
              </div>
            </div>
            @endif
          </td>
        </tr>
        @endif
        @endforeach
        </table>
      </div>
    @endif
    @endif
--}}
