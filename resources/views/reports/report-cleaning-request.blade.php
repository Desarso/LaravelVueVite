@extends('layouts/contentLayoutMaster')

@section('icon')
<i class="fad fa-chart-bar" style="color: #FD7E14"></i>
@endsection

@section('title', 'Reporte Solicitudes de Limpieza')

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
        <div class="col-4 col-sm-4">
            <label for="dropDownListItem">Item</label>
            <input id="dropDownListItem" class="form-control" title="Filtro por item" style="width: 100%;">
        </div>
        <div class="col-4 col-sm-4">
            <label for="dropDownListTicketType">Tipo de solicitud</label>
            <input id="dropDownListTicketType" class="form-control" title="Filtro por Tipo de solicitud" style="width: 100%;">
        </div>
        <div class="col-4 col-sm-4">
            <label for="dropDownListSpot">Lugar</label>
            <input id="dropDownListSpot" class="form-control" title="Filtro por responsable" style="width: 100%;">
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 col-12">
            <div class="card" style="margin-bottom: 1rem !important;">
                <div class="card-header">
                    <h4 class="card-title">Solicitudes más frecuentes</h4>
                </div>
                <div class="card-content">
                    <div class="card-body" style="padding-top:0px; padding-bottom:0px;">
                        <div id="item-column-chart"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-12">
            <div class="card" style="margin-bottom: 1rem !important; max-height: 370px;">
                <div class="card-header d-flex justify-content-between align-items-end">
                    <h4 class="mb-0">Tipo de solicitudes</h4>
                    <p class="font-medium-5 mb-0"><i class="feather icon-help-circle text-muted cursor-pointer"></i></p>
                </div>
                <div class="card-content">
                    <div class="card-body px-0 pb-0" style="">
                        <div id="ticket-type-chart"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-12">
            <div class="card" style="margin-bottom: 1rem !important;">
                <div class="card-header">
                    <h4 class="card-title">Lista de solicitudes
                    </h4>
                </div>
                <div class="card-content">
                    <div class="card-body" style="position: relative;">
                        <div id="gridCleaningRequest" style="width:100%"></div>
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
<script src="{{ asset('js/reports/report-cleaning-request.js') }}"></script>
<script src="{{ asset('daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('js/reports/utils.js') }}"></script>
@endsection