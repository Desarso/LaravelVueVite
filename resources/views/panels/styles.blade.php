<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600">
<link rel="stylesheet" href="vendors/css/vendors.min.css">
<link rel="stylesheet" href="vendors/css/ui/prism.min.css">
<link rel="stylesheet" href="fonts/fontawesome/css/all.min.css"  />


{{-- Vendor Styles --}}
@yield('vendor-style')
{{-- Theme Styles --}}
<link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset(('css/bootstrap-extended.css')) }}">
<link rel="stylesheet" href="{{ asset(('css/colors.css')) }}">
<link rel="stylesheet" href="{{ asset(('css/components.css')) }}">
<link rel="stylesheet" href="{{ asset(('css/themes/dark-layout.css')) }}">
<link rel="stylesheet" href="{{ asset(('css/themes/semi-dark-layout.css')) }}">
<link rel="stylesheet" href="{{ asset(('css/app.css')) }}">
{{-- Kendo Style --}}
<link rel="stylesheet" href="{{ asset(('vendors/kendo/styles/kendo.bootstrap-v4.min.css')) }}">
<link rel="stylesheet" href="{{ asset(('css/kendo-custom.css')) }}">
{{-- Responsive Style --}}
<link rel="stylesheet" href="{{ asset(('css/responsive.css')) }}">
{{-- Whagons General --}}
<link rel="stylesheet" href="{{ asset(('css/custom/custom.css')) }}">
<link rel="stylesheet" href="{{ asset(('css/custom/treeview.css')) }}">

<!-- input-file-->
<link href="{{ asset('input-file/fileinput.min.css') }}" rel="stylesheet" type="text/css">
<!-- input-file-->

<!-- sweetalert2 -->
<link rel="stylesheet" href="{{ asset(('vendors/css/extensions/sweetalert2.min.css')) }}">

<!-- toastr -->
<link rel="stylesheet" href="{{ asset(('vendors/css/extensions/toastr.css')) }}">
<link rel="stylesheet" href="{{ asset(('css/plugins/extensions/toastr.css')) }}">

{{-- {!! App\Helpers\Helper::applClasses() !!} --}}
@php
$configData = App\Helpers\Helper::applClasses();
@endphp

{{-- Layout Styles works when don't use customizer --}}

{{-- @if($configData['theme'] == 'dark-layout')
        <link rel="stylesheet" href="{{ asset(('css/themes/dark-layout.css')) }}">
@endif
@if($configData['theme'] == 'semi-dark-layout')
<link rel="stylesheet" href="{{ asset(('css/themes/semi-dark-layout.css')) }}">
@endif --}}
{{-- Page Styles --}}
@if($configData['mainLayoutType'] === 'horizontal')
<link rel="stylesheet" href="{{ asset(('css/core/menu/menu-types/horizontal-menu.css')) }}">
@endif
<link rel="stylesheet" href="{{ asset(('css/core/menu/menu-types/vertical-menu.css')) }}">
<link rel="stylesheet" href="{{ asset(('css/core/colors/palette-gradient.css')) }}">
{{-- Page Styles --}}
@yield('page-style')
{{-- Laravel Style --}}
<link rel="stylesheet" href="{{ asset(('css/custom-laravel.css')) }}">
{{-- Custom RTL Styles --}}
@if($configData['direction'] === 'rtl' && isset($configData['direction']))
<link rel="stylesheet" href="{{ asset(('css/custom-rtl.css')) }}">
@endif