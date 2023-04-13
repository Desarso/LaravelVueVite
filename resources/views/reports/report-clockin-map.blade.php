@extends('layouts/contentLayoutMaster')

@section('icon')
<i class="far fa-map-marker-alt"></i>
@endsection

@section('title', 'Clock-In Dashboard')

@section('page-style')
<link rel="stylesheet" href="{{ asset(mix('kendo/kendo-custom-config.css')) }}">
@endsection

@section('vendor-style')

<!-- vendor css files -->
<link rel="stylesheet" href="{{ asset(mix('vendors/css/charts/apexcharts.css')) }}">
<link rel="stylesheet" href="{{ asset('daterangepicker/daterangepicker.css') }}">

<style>
    div.k-grid-norecords {
        display: block !important;
    }

    .k-listview > .k-state-selected {
        background: #7367f0 !important;
    }

    .k-listview>.k-state-selected h6 {
        color: white !important;
    }

    #listViewUsers {
        overflow: auto;
    }

    .user-photo {
        display: inline-block;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background-size: 32px 35px;
        background-position: center center;
        vertical-align: middle;
        line-height: 32px;
        box-shadow: inset 0 0 1px #999, inset 0 0 10px rgba(0, 0, 0, .2);
        margin-left: 5px;
    }

    .user-fullname {
        display: inline-block;
        vertical-align: middle;
        line-height: 32px;
        padding-left: 3px;
    }

</style>

@endsection

@section('breadcrum-right')
<div class="mb-1" id="dateRangePicker">
    <i class="fa fa-calendar"></i>&nbsp;
    <span></span> <i class="fa fa-caret-down"></i>
</div>
@endsection

@section('content')

<div class="container-fluid">
    <div class="row mb-1">
        <div class="col-1 col-sm-1">
            <button id="btn-excel" type="button" class="btn btn-icon btn-success mr-1 waves-effect waves-light"
                data-toggle="tooltip" data-placement="top" title="" data-original-title="Exportar Excel"
                style="margin-top: 1rem;">
                <i class="fas fa-file-excel"></i>
            </button>
        </div>
        <div class="col-4 col-sm-4">
            <label for="dropDownListUser">Usuario</label>
            <input id="dropDownListUser" class="form-control" title="Filtro por usuario" style="width: 100%;">
        </div>
        <div class="col-4 col-sm-4">
            <label for="dropDownListTeam">Equipo</label>
            <input id="dropDownListTeam" class="form-control" title="Filtro por equipo" style="width: 100%;">
        </div>
    </div>

    <div class="row">
        <div class="col-md-7 col-7">
            <div class="" id="gridClockinLog" style="width:100%;"></div>
        </div>
        <div class="col-md-5 col-5">
            <div id="map" style="width:96%; height:100%; position: absolute;"></div>
        </div>
    </div>

</div>
<script type="text/x-kendo-template" id="template">
    <div class="card border-primary text-center bg-transparent" style="max-height:160px; width:44%; float:left; margin:5px 15px;">
        <div class="card-content d-flex">
            <div class="card-body">
                <img src="#:urlpicture#" alt="element 06" width="80px" height="80px" class="mb-1" style="border-radius: 50%;">
                <h6 class="card-subtitle text-muted">#:text#</h6>
            </div>
        </div>
    </div>
</script>
@endsection

@section('page-script')
<script src="{{ asset('js/reports/report-clockin-map.js') }}"></script>
<script src="{{ asset('daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('js/reports/utils.js') }}"></script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAZoNhuZaqtozVGDBuRC206IvCyqyeI2MU&callback=initMap"> </script>
<!-- <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAZoNhuZaqtozVGDBuRC206IvCyqyeI2MU&callback=initMap"> </script> -->
@endsection