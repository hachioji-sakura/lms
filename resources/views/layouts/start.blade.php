@section('start')
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<title>@yield('title') | {{__('labels.system_name')}}</title>
<!-- layouts.start start-->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="{{asset('favicon.ico')}}" type="image/x-icon">
<link rel="apple-touch-icon" href="{{asset('apple-touch-icon.png')}}" sizes="180x180">
<link rel="icon" type="image/png" href="{{asset('android-touch-icon.png')}}" sizes="192x192">
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
<!-- Date Picker -->
<link rel="stylesheet" href="{{asset('js/plugins/datepicker/datepicker3.css')}}">

<!-- fullCalendar 2.2.5-->
<link rel="stylesheet" href="{{asset('js/plugins/fullcalendar/fullcalendar.min.css?v=1')}}">
<link rel="stylesheet" href="{{asset('js/plugins/fullcalendar/fullcalendar.print.css?v=1')}}" media="print">

<!-- Styles -->
<!-- link href="{{asset('css/app.css') }}" rel="stylesheet" -->

<!-- Ion Slider -->
<link rel="stylesheet" href="{{asset('js/plugins/ionslider/ion.rangeSlider.css')}}">
<!-- ion slider Nice -->
<link rel="stylesheet" href="{{asset('js/plugins/ionslider/ion.rangeSlider.skinNice.css')}}">
<!-- bootstrap slider -->
<link rel="stylesheet" href="{{asset('js/plugins/bootstrap-slider/slider.css')}}">

<link rel="stylesheet" href="{{asset('js/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css')}}">

<!-- Google Font: Source Sans Pro -->
<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

<!-- Theme style -->
<link rel="stylesheet" href="{{asset('dist/css/adminlte.css?v=3')}}">


<!-- jQuery -->
<script src="{{asset('js/plugins/jquery/jquery.min.js')}}"></script>
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<!-- Push.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/push.js/1.0.9/push.min.js"></script>
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
<!-- daterangepicker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>
<script src="{{asset('js/plugins/daterangepicker/daterangepicker.js')}}"></script>
<!-- datepicker -->
<script src="{{asset('js/plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('js\plugins\datepicker\locales\bootstrap-datepicker.ja.js')}}"></script>
<!-- bootstrap time picker -->
<script src="{{asset('js/plugins/timepicker/bootstrap-timepicker.min.js')}}"></script>
<!-- Slimscroll -->
<script src="{{asset('js/plugins/slimScroll/jquery.slimscroll.min.js')}}"></script>
<!-- FastClick -->
<script src="{{asset('js/plugins/fastclick/fastclick.js')}}"></script>
<!-- fullCalendar 2.2.5 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>
<script src="{{asset('js/plugins/fullcalendar/fullcalendar.js?v=1')}}"></script>
<!-- Ion Slider -->
<script src="{{asset('js/plugins/ionslider/ion.rangeSlider.min.js')}}"></script>
<!-- CK Editor -->
<script src="{{asset('js/plugins/ckeditor/ckeditor.js')}}"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="{{asset('js/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js')}}"></script>

<!-- AdminLTE App -->
<script src="{{asset('dist/js/adminlte.js')}}"></script>

</head>
<!-- layouts.start end-->
@endsection
