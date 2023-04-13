@isset($pageConfigs)
{!! App\Helpers\Helper::updatePageConfig($pageConfigs) !!}
@endisset

<!DOCTYPE html>
{{-- {!! Helper::applClasses() !!} --}}
@php
$configData = App\Helpers\Helper::applClasses();
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
    @include('panels/styles')

</head>
<script>
    //Setting global lists in all views
    window.global_statuses     = {!! $global_statuses !!};
    window.global_items        = {!! $global_items !!};
    window.global_priorities   = {!! $global_priorities !!};
    window.global_spots        = {!! $global_spots !!};
    window.global_spot_types   = {!! $global_spot_types !!};
    window.global_users        = {!! $global_users !!};
    window.global_teams        = {!! $global_teams !!};
    window.global_ticket_types = {!! $global_ticket_types !!};
    window.global_permissions  = {!! $global_permissions !!};
    window.global_tags         = {!! $global_tags !!};
    window.global_assets       = {!! $global_assets !!};
    window.global_checklist    = {!! $global_checklist !!};
    window.global_roles        = {!! $global_roles !!};
    //Setting global lists in all views

    //Setting global user
    window.user = {!! Auth::user() !!}

</script>

@yield('content')
 

{{-- include footer --}}
  @include('panels/footer')

  {{-- include default scripts --}}
  @include('panels/scripts')
