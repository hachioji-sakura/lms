@section('start')
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<title>@yield('title') | 学習管理システム</title>
<!-- layouts.start start-->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Scripts -->
<!-- script src="{{asset('js/app.js') }}" defer></script -->

<!-- Font Awesome -->
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
<!-- Ionicons -->
<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
<!-- Fonts -->
<link rel="dns-prefetch" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

<!-- Bootstrap time Picker -->
<link rel="stylesheet" href="{{asset('js/plugins/timepicker/bootstrap-timepicker.min.css')}}">
<!-- Select2 -->
<link rel="stylesheet" href="{{asset('js/plugins/select2/select2.min.css')}}">
<!-- iCheck for checkboxes and radio inputs -->
<link rel="stylesheet" href="{{asset('js/plugins/iCheck/all.css')}}">


<!-- Styles -->
<!-- link href="{{asset('css/app.css') }}" rel="stylesheet" -->

<!-- Theme style -->
<link rel="stylesheet" href="{{asset('dist/css/adminlte.css')}}">
<!-- Google Font: Source Sans Pro -->
<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
<!-- jQuery -->
<script src="{{asset('js/plugins/jquery/jquery.min.js')}}"></script>
<!-- Bootstrap 4 -->
<script src="{{asset('js/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- Select2 -->
<script src="{{asset('js/plugins/select2/select2.full.min.js')}}"></script>
<!-- iCheck 1.0.1 -->
<script src="{{asset('js/plugins/iCheck/icheck.min.js')}}"></script>
<!-- InputMask -->
<script src="{{asset('js/plugins/input-mask/jquery.inputmask.js')}}"></script>
<script src="{{asset('js/plugins/input-mask/jquery.inputmask.date.extensions.js')}}"></script>
<script src="{{asset('js/plugins/input-mask/jquery.inputmask.extensions.js')}}"></script>
<!-- bootstrap time picker -->
<script src="{{asset('js/plugins/timepicker/bootstrap-timepicker.min.js')}}"></script>
<!-- Slimscroll -->
<script src="{{asset('js/plugins/slimScroll/jquery.slimscroll.min.js')}}"></script>
<!-- FastClick -->
<script src="{{asset('js/plugins/fastclick/fastclick.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('dist/js/adminlte.js')}}"></script>

</head>
<!-- layouts.start end-->
@endsection
