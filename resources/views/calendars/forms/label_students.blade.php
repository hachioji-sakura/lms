@foreach($item["students"] as $member)
  @if($member->user->details()->role!="student") @continue @endif

  @if($user->role=="manager" || $user->role=="teacher")
  <a target="_blank" alt="student_name" href="/students/{{$member->user->details('students')->id}}" class="text-{{config('status_style')[$member->status]}}"

  >
  @else
  <span>
  @endif
    @if($member->status=='new' || $member->status=='confirm' || $member->status=='fix')
    <i class="fa fa-user-graduate"></i>
    @elseif($member->status=='cancel')
    <i class="fa fa-ban"></i>
    @elseif($member->status=='presence')
    <i class="fa fa-check-circle"></i>
    @elseif($member->status=='absence')
    <i class="fa fa-calendar-times"></i>
    @elseif($member->status=='rest' || $member->status=='lecture_cancel')
    <i class="fa fa-user-times" title="{{$member->exchange_limit_date}}"></i>
    @endif
    {{$member->user->details('students')->name}}
    @if($status_visible==true)
      @if(isset($user) && ($user->role=="teacher" || $user->role=="manager") && $member->is_rest_status()==true && !empty(trim($member->get_rest_result())))
      ({{$member->get_rest_result()}})
      @else
      <small title="{{$item["id"]}}" class="badge badge-{{config('status_style')[$member->status]}} mt-1" title="{{$member->status}}">{{$member->status_name}}</small>
      @endif
    @endif
  @if($user->role=="manager" || $user->role=="teacher")
    @if($member->user->student->get_status($item->start_time)!='regular')
      <small title="{{$member->remark}}" 
        class="badge badge-{{config('status_style')[$member->user->student->get_status($item->start_time)]}} mt-1" 
        >{{$member->user->student->get_status_name($item->start_time)}}</small>
    @endif
  </a>
  @else
  </span>
  @endif
  @if($set_br==true)
  <br>
  @else
  &nbsp;
  @endif
@endforeach
