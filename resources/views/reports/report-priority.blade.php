@extends('layouts/contentLayoutMaster')

@section('icon')
<i class="fad fa-chart-bar" style="color: #FD7E14"></i>
@endsection

@section('title', 'Reporte Prioridades')

@section('vendor-style')

<!-- vendor css files -->
<link rel="stylesheet" href="{{ asset(mix('vendors/css/charts/apexcharts.css')) }}">
<link rel="stylesheet" href="{{ asset('daterangepicker/daterangepicker.css') }}">

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

    <div class="row mb-1">
        <div class="col-4 col-sm-3">
            <label for="dropDownListPriority">Prioridad</label>
            <input id="dropDownListPriority" class="form-control" title="Filtro por prioridad" style="width: 100%;">
        </div>
        <div class="col-4 col-sm-3">
            <label for="dropDownListStatus">Estado</label>
            <input id="dropDownListStatus" class="form-control" title="Filtro por estado" style="width: 100%;">
        </div>
        <div class="col-4 col-sm-3">
            <label for="dropDownListTeam">Equipo</label>
            <input id="dropDownListTeam" class="form-control" title="Filtro por equipo" style="width: 100%;">
        </div>
        <div class="col-4 col-sm-3">
            <label for="dropDownListUser">Responsable</label>
            <input id="dropDownListUser" class="form-control" title="Filtro por responsable" style="width: 100%;">
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 col-12">
            <div class="card" style="margin-bottom: 1rem !important;">
                <div class="card-header d-flex justify-content-between align-items-end">
                    <h4 class="mb-0">Eficiencia General</h4>
                    <p class="font-medium-5 mb-0"><i class="feather icon-help-circle text-muted cursor-pointer"></i></p>
                </div>
                <div class="card-content">
                    <div class="card-body px-0 pb-0" style="padding-top: 0px;">
                        <div id="efficacy-chart" class="mt-75"></div>
                        <div class="row text-center mx-0">
                            <div class="col-4 border-top border-right d-flex align-items-between flex-column pt-1">
                                <p class="mb-50">Total</p>
                                <p id="total-tickets" class="font-large-1 text-bold-700" style="margin-bottom: 0.5rem !important;">0</p>
                            </div>
                            <div class="col-4 border-top border-right d-flex align-items-between flex-column pt-1">
                                <p class="mb-50">Retrasadas</p>
                                <p id="total-delayed-tickets" class="font-large-1 text-bold-700" style="margin-bottom: 0.5rem !important;">0</p>
                            </div>
                            <div class="col-4 border-top d-flex align-items-between flex-column pt-1">
                                <p class="mb-50">Pospuestas</p>
                                <p id="total-postponed-tickets" class="font-large-1 text-bold-700" style="margin-bottom: 0.5rem !important;">0</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8 col-12">
            <div class="card" style="margin-bottom: 1rem !important;">
                <div class="card-header">
                    <h4 class="card-title">Datos por prioridad</h4>
                </div>
                <div class="card-content">
                    <div class="card-body" style="position: relative;">
                        <div id="gridPriority" style="width:100%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-12">
            <div class="card" style="margin-bottom: 1rem !important;">
                <div class="card-header">
                    <h4 class="card-title">Lista de tareas
                    </h4>
                </div>
                <div class="card-content">
                    <div class="card-body" style="position: relative;">
                        <div id="gridTicketPriority" style="width:100%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Datos por responsables
                    </h4>
                </div>
                <div class="card-content">
                    <div class="card-body" style="position: relative;">
                        <div id="gridUserPriority" style="width:100%"></div>
                    </div>
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
{{-- Page js files --}}
<script src="{{ asset('js/reports/report-priority.js') }}"></script>
<script src="{{ asset('daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('js/reports/utils.js') }}"></script>
@endsection