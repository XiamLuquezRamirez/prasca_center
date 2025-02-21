<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="{{ asset('app-assets/images/favicon.ico') }}"> 

    <title>@yield('title', 'Inicio') - Prasca Center</title>

	<!-- Vendors Style-->
	<link rel="stylesheet" rel="preload" href="{{ asset('app-assets/css/vendors_css.css') }}">

	<!-- Style-->
	<link rel="stylesheet" rel="preload" href="{{ asset('app-assets/css/style.css') }}">
	<link rel="stylesheet" rel="preload" href="{{ asset('app-assets/css/skin_color.css') }}">
	<link rel="stylesheet" rel="preload" href="{{ asset('app-assets/css/custom.css') }}">
	<link rel="stylesheet" rel="preload" href="{{ asset('app-assets/css/dashboard7.css') }}">

  <meta name="csrf-token" content="{{ csrf_token() }}">
  
  </head>