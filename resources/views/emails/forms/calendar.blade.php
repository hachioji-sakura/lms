@if($item->trial_id > 0)
■体験授業
@endif
日時：{{$item['datetime']}}
講師：{{$item['teacher_name']}}
レッスン：{{$item['lesson']}}
コース：{{$item['course']}}
科目：{{implode(',', $item['subject'])}}
生徒：
@foreach($item['students'] as $member)
  {{$member->user->details('students')->name()}}:  {{$member->status_name()}}
@endforeach
@if(isset($item['cancel_reason']) && !empty($item['cancel_reason']))
キャンセル理由:{{$item['cancel_reason']}}
@endif
@if(isset($item['rest_reason']) && !empty($item['rest_reason']))
休み理由:{{$item['rest_reason']}}
@endif
@isset($item['comment'])
連絡事項：{{$item['comment']}}
@endisset
