@extends('layouts.contentLayoutMaster')

@section('icon')
<i class="fad fa-chart-bar" style="color: #FD7E14"></i>
@endsection

@section('title', 'Resumen de Equipos')

@section('vendor-style')

<!-- vendor css files -->
<link rel="stylesheet" href="{{ asset(mix('vendors/css/charts/apexcharts.css')) }}">
<link rel="stylesheet" href="{{ asset('daterangepicker/daterangepicker.css') }}">
@endsection

@section('page-style')
<link rel="stylesheet" href="{{ asset(mix('css/pages/card-analytics.css')) }}">
<style>
    
    div.k-grid-norecords {
        display: block !important;
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

    <div class="row mb-1" style="margin-left: -29px; margin-right: -29px;">
        <div class="col-4 col-sm-3">
            <label for="dropDownListSpot">Sede</label>
            <input id="dropDownListSpot" class="form-control" title="Filtro por sede" style="width: 100%;">
        </div>
        <div class="col-4 col-sm-3">
            <label for="dropDownListTeam">Equipo</label>
            <input id="dropDownListTeam" class="form-control" title="Filtro por equipo" style="width: 100%;">
        </div>
        <div class="col-4 col-sm-3">
            <label for="dropDownListUser">Usuario</label>
            <input id="dropDownListUser" class="form-control" title="Filtro por usuarios" style="width: 100%;">
        </div>
        <div class="col-4 col-sm-3">
            <label for="dropDownListItem">Tarea</label>
            <input id="dropDownListItem" class="form-control" title="Filtro por tarea" style="width: 100%;">
        </div>
    </div>

    <div class="row">

        <div class="col-lg-3 col-md-6 col-12" style="padding: 0px 10px 0px 0px;">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-end">
                    <h4 class="mb-0">Distribución de tareas</h4>
                    <p class="font-medium-5 mb-0"></p>
                </div>
                <div class="card-content">
                    <div class="card-body" style="padding-top:0px; padding-bottom:0px;">
                        <div id="chart-pie-teams-summary"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-9 col-md-6 col-sm-12 col-12" style="padding: 0px 0px 0px 0px;">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-end">
                    <h4 class="mb-0" class="card-title">Cumplimiento mensual</h4>
                </div>
                <div class="card-content">
                    <div class="card-body" style="padding-top:0px; padding-bottom:0px;">
                        <div id="chart-bars-teams-summary"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="card">
            <div class="card-content">
                <div class="card-body" style="display: inline-flex;">
                    <div class="col-sm-7 col-md-7">
                        <div id="gridTeamsSummary"></div>
                    </div>
                    <div class="col-sm-5 col-md-5">
                        <div id="gridTeamUserSummary"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @endsection

    @section('vendor-script')
    <!-- vendor files -->
    <script src="{{ asset(mix('vendors/js/charts/apexcharts.min.js')) }}"></script>
    @endsection

    @section('page-script')
    <script src="{{ asset('js/reports/report-teams-summary.js') }}"></script>
    <script src="{{ asset('daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('js/reports/utils.js') }}"></script>
    @endsection