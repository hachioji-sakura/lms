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
  20,000円（税抜き）
@elseif($user->has_tag('lesson',2)==true  && $user->has_tag('english_talk_lesson','chinese')==false)
  {{-- 英会話(中国語以外） --}}
  15,000円（税抜き）
@elseif($user->has_tag('lesson',2)==true && $user->has_tag('english_talk_lesson','chinese')==true)
  {{-- 中国語 --}}
  15,000円（税抜き）
@elseif($user->has_tag('lesson',4)==true && $user->has_tag('kids_lesson','infant_lesson')==true)
  {{-- 幼児教室 --}}
  15,000円（税抜き）
@elseif($user->has_tag('lesson',4)==true && $user->has_tag('kids_lesson','abacus')==true)
  {{-- そろばん --}}
  10,000円（税抜き）
@elseif($user->has_tag('lesson',3)==true)
  {{-- ピアノ --}}
  10,000円（税抜き）
@elseif($user->has_tag('lesson',4)==true && $user->has_tag('kids_lesson','dance')==true)
  {{-- ダンス --}}
  5,000円（税抜き）
@else
  0円
@endif
