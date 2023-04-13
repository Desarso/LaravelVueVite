@extends('layouts/contentLayoutMaster')

@section('icon')
<i class="fad fa-broom" style="color: #FD7E14"></i>
@endsection

@section('title', 'Reporte de Limpieza')

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
            <label for="dropDownListSpot">Lugar</label>
            <input id="dropDownListSpot" class="form-control" title="Filtro por responsable" style="width: 100%;">
        </div>
        <div class="col-4 col-sm-3">
            <label for="dropDownListItem">Tipo de limpieza</label>
            <input id="dropDownListItem" class="form-control" title="Filtro por tipo de limpieza" style="width: 100%;">
        </div>
        <div class="col-4 col-sm-3">
            <label for="dropDownListUser">Usuario</label>
            <input id="dropDownListUser" class="form-control" title="Filtro por usuario" style="width: 100%;">
        </div>
        <div class="col-4 col-sm-3">
            <label for="dropDownListStatus">Estado</label>
            <input id="dropDownListStatus" class="form-control" title="Filtro por estado" style="width: 100%;">
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-12">
            <div class="card" style="margin-bottom: 1rem !important;">
                <div class="card-header">
                    <h4 class="card-title">Lista de limpiezas
                    </h4>
                </div>
                <div class="card-content">
                    <div class="card-body" style="position: relative;">
                        <div id="gridCleaning" style="width:100%"></div>
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
<script>
    window.cleaningStatuses = {!! $cleaningStatuses!!};
    window.cleaningItems    = {!! $cleaningItems!!};
</script>
<script src="{{ asset('js/reports/report-cleaning.js') }}"></script>
<script src="{{ asset('daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('js/reports/utils.js') }}"></script>
@endsection