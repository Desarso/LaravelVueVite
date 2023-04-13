@extends('layouts.contentLayoutMaster')

@section('icon')
<i class="fad fa-chart-bar" style="color: #FD7E14"></i>
@endsection

@section('title', 'Resumen de Spots')

@section('vendor-style')

<!-- vendor css files -->
<link rel="stylesheet" href="{{ asset(mix('vendors/css/charts/apexcharts.css')) }}">
<link rel="stylesheet" href="{{ asset('daterangepicker/daterangepicker.css') }}">
@endsection

@section('page-style')
<link rel="stylesheet" href="{{ asset(mix('css/pages/card-analytics.css')) }}">
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
        <div class="col-4 col-sm-3">
            <label for="dropDownListSpot">Sede</label>
            <input id="dropDownListSpot" class="form-control" title="Filtro por sede" style="width: 100%;">
        </div>
        <div class="col-4 col-sm-3">
            <label for="dropDownListTeam">Equipo</label>
            <input id="dropDownListTeam" class="form-control" title="Filtro por equipo" style="width: 100%;">
        </div>
        <div class="col-4 col-sm-3">
            <label for="dropDownListTicketType">Tipo de tarea</label>
            <input id="dropDownListTicketType" class="form-control" title="Filtro por tipo de tarea" style="width: 100%;">
        </div>
        <div class="col-4 col-sm-3">
            <label for="dropDownListItem">Tarea</label>
            <input id="dropDownListItem" class="form-control" title="Filtro por tarea" style="width: 100%;">
        </div>
    </div>

    <div class="row">

        <div class="col-lg-3 col-md-6 col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-end">
                    <h4 class="mb-0">Cumplimiento General</h4>
                    <p class="font-medium-5 mb-0"></p>
                </div>
                <div class="card-content">
                    <div class="card-body" style="padding-top:0px; padding-bottom:0px;">
                        <div id="chart-pie-tickets-summary"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-9 col-md-6 col-sm-12 col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-end">
                    <h4 class="mb-0" class="card-title">Cumplimiento mensual</h4>
                </div>
                <div class="card-content">
                    <div class="card-body" style="padding-top:0px; padding-bottom:0px;">
                        <div id="chart-bars-tickets-summary"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="card">
            <div class="card-content">
                <div class="card-body" style="display: inline-flex;">
                    <div class="col-sm-4 col-md-4">
                        <div id="gridSpotSummary"></div>
                    </div>
                    <div class="col-sm-4 col-md-4">
                        <div id="gridTicketTypeSummary"></div>
                    </div>
                    <div class="col-sm-4 col-md-4">
                        <div id="gridItemSummary"></div>
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
    <script src="{{ asset('js/reports/report-tasks-summary.js') }}"></script>
    <script src="{{ asset('daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('js/reports/utils.js') }}"></script>
    @endsection