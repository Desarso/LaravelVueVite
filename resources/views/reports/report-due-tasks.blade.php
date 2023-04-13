@extends('layouts/contentLayoutMaster')

@section('icon')
<i class="fad fa-chart-bar" style="color: #FD7E14"></i>
@endsection

@section('title', 'Reporte tareas vencidas')

@section('vendor-style')
<!-- vendor css files -->
<link rel="stylesheet" href="{{ asset('daterangepicker/daterangepicker.css') }}">

<style>
    
    div.k-grid-norecords {
        display: block !important;
    }

    .blink {
        animation: blinker 2s linear infinite;
        color: #ea5455 !important;
        font: bold !important;
        font-weight: 900;
        width: 200px;
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
        <input name="group" id="group" hidden />
        <div class="col-3 col-sm-3">
            <label for="dropDownListBranch">Sede</label>
            <select id="dropDownListBranch" class="form-control" name="dropDownListBranch" style="width: 100%;"></select>
        </div>
        <div class="col-3 col-sm-3">
            <label for="dropDownListTeam">Equipo</label>
            <input id="dropDownListTeam" class="form-control" title="Filtro por equipo" style="width: 100%;">
        </div>
        <div class="col-3 col-sm-3">
            <label for="dropDownListItem">Tarea</label>
            <input id="dropDownListItem" class="form-control" title="Filtro por tarea" style="width: 100%;">
        </div>
        <div class="col-3 col-sm-3">
            <label for="dropDownListUser">Usuario</label>
            <input id="dropDownListUser" class="form-control" title="Filtro por usuarios" style="width: 100%;">
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-sm-12">
            <div id="gridSpot" style=""></div>
        </div>
    </div>

    <div class="row mt-1">
        <div class="col-6 col-sm-6">
            <div id="gridTeam" style="width:100%;"></div>
        </div>
        <div class="col-6 col-sm-6">
            <div id="gridItem" style="width:100%;"></div>
        </div>
    </div>

    <div class="row mt-1">
        <div class="col-12 col-sm-12">
            <div id="gridTicket" style=""></div>
        </div>
    </div>

</div>
@endsection

@section('page-script')
{{-- Page js files --}}
<script src="{{ asset('js/reports/utils.js') }}"></script>
<script src="{{ asset('js/reports/report-due-tasks.js') }}"></script>
<script src="{{ asset('daterangepicker/daterangepicker.js') }}"></script>
@endsection