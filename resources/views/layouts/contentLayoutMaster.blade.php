@isset($pageConfigs)
{!! App\Helpers\Helper::updatePageConfig($pageConfigs) !!}
@endisset

<!DOCTYPE html>
{{-- {!! App\Helpers\Helper::applClasses() !!} --}}
@php
$configData =App\Helpers\Helper::applClasses();
@endphp

<html lang="@if(session()->has('locale')){{session()->get('locale')}}@else{{$configData['defaultLanguage']}}@endif"
    data-textdirection="{{ env('MIX_CONTENT_DIRECTION') === 'rtl' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Whagons Studio</title>
    <link rel="shortcut icon" type="image/x-icon" href="images/logo/favicon.ico">

    {{-- Include core + vendor Styles --}}
 

</head>
<script>
    console.log("hello world")
</script>

@isset($configData["mainLayoutType"])
@extends((( $configData["mainLayoutType"] === 'horizontal') ? 'layouts.horizontalLayoutMaster' :
'layouts.verticalLayoutMaster' ))
@endisset

