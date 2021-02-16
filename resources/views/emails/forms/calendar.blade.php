@if(isset($notice) && !empty($notice))
■{{__('labels.notice')}}:
{{$notice}}
@endif

@if($item->is_teaching()==true)
■{{$item->teaching_type_name()}}
@else
■{{$item->work()}}
@endif
{{__('labels.datetime')}}：{{$item->datetime()}}
{{__('labels.place')}}：{{$item->place_floor_name()}}@if($item->is_online()==true)/{{__('labels.online')}}@endif

@if($send_to!=='student')
({{__('labels.status')}}：{{$item->status_name()}})
@endif
（{{__('labels.details')}}）
@if($item->is_teaching()==true)
{{__('labels.teachers')}}：{{$item->user->details('teachers')->name()}}
{{__('labels.lesson')}}：{{$item->lesson()}}
{{__('labels.lesson_type')}}：{{$item->course()}}
{{__('labels.subject')}}：{{implode(',', $item->subject())}}
@endif
@if($send_to!=='student')
{{__('labels.students')}}：
@foreach($item->members as $member)
@if($member->user->details('students')->role=="student")
{{$member->user->details('students')["name"]}}({{$member->status_name()}})
@endif
@endforeach
@endif

@if($send_to!=='student' && (!isset($is_control) || $is_control==false))
({{__('labels.control')}}：{{$login_user["name"]}})
@endif
@if($send_to=='student')
@if($item->trial_id > 0 && $item->has_tag('lesson', 1)==true)

■持ち物
1. 教科書、学校で使用している問題集
2. 筆記用具
3. ノート
4. 過去の定期試験と模試
@endif
@if($item->is_online()==true && $item->user->has_tag('skype_name'))

■オンライン授業について
オンライン授業は、スカイプを利用いたします。
スカイプがダウンロードされたパソコン・ダブレット・スマホで、講師のSkype名にてアカウントを検索していただけますと、
担当講師と繋がります。

※講師のSkype Name：{{$item->user->get_tag_value('skype_name')}}
@endif
@endif
