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
