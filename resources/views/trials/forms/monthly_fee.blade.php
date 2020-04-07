{{--
  塾       入会金：20000 / 会費：2000
  英会話： 入会金：15000 / 会費：2000
  中国語   入会金 : 15000 /会費： 1500
  幼児教室 入会金：15000 / 会費：2000
  そろばん 入会金：10000 / 会費：1500
  ピアノ:  入会金：10000 / 会費：1500
  ダンス   入会金：5000  / 会費：500
  ※金額降順にしておく必要がある
--}}
@if($user->has_tag('lesson',1)==true)
  {{-- 塾 --}}
  <span title="lesson=1">2,000円</span>
@elseif($user->has_tag('lesson',2)==true  && $user->has_tag('english_talk_lesson','chinese')==false)
  {{-- 英会話(中国語以外） --}}
  <span title="lesson=2">2,000円</span>
@elseif($user->has_tag('lesson',4)==true && $user->has_tag('kids_lesson','infant_lesson')==true)
  {{-- 幼児教室 --}}
  <span title="lesson=4/infant_lesson">2,000円</span>
@elseif($user->has_tag('lesson',2)==true && $user->has_tag('english_talk_lesson','chinese')==true)
  {{-- 中国語 --}}
  <span title="lesson=2/chinese">1,500円</span>
@elseif($user->has_tag('lesson',4)==true && $user->has_tag('kids_lesson','abacus')==true)
  {{-- そろばん --}}
  <span title="lesson=4/abacus">1,500円</span>
@elseif($user->has_tag('lesson',3)==true)
  {{-- ピアノ --}}
  1,500円
@elseif($user->has_tag('lesson',4)==true && $user->has_tag('kids_lesson','dance')==true)
  {{-- ダンス --}}
  500円
@else
  0円
@endif
