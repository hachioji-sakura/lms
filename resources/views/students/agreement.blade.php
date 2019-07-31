@extends('layouts.simplepage')
@section('title')
  ご契約情報
@endsection
@section('title_header')
<ol class="step">
  <li id="step_input" class="is-current">@yield('title')</li>
</ol>
@endsection
@section('content')
@component('students.forms.agreement', ['item' => $student, 'fields' => $fields, 'domain' => $domain]) @endcomponent

@endsection
