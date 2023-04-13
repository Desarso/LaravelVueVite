@extends('layouts/contentLayoutMaster')

@section('icon')
<i class="fad fa-users" style="color: #FD7E14"></i>
@endsection

@section('title', 'Reporte de Tareas')

@section('vendor-style')

<!-- vendor css files -->
<link rel="stylesheet" href="{{ asset(mix('vendors/css/charts/apexcharts.css')) }}">
<link rel="stylesheet" href="{{ asset('daterangepicker/daterangepicker.css') }}">

<style>

    .k-header .k-link{
        text-align: center;
    }

    div.k-grid-norecords {
        display: block !important;
    }

    .ticket-total{
        text-align: center! important;
    }

    .ticket-pendint{
        background-color: rgba(234, 84, 85, 0.15) !important;
        text-align: center! important;
    }

    .ticket-progress{
        background-color: rgba(40, 199, 111, 0.15) !important;
        text-align: center! important;
    }

    .ticket-paused{
        background-color: rgba(255, 159, 67, 0.15) !important;
        text-align: center! important;
    }

    .ticket-finished{
        background-color: rgba(132, 127, 131, 0.15) !important;
        text-align: center! important;
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
            <input id="dropDownListSpot" class="form-control" title="Filtro por lugar" style="width: 100%;">
        </div>
        <div class="col-4 col-sm-3">
            <label for="dropDownListItem">Ítem</label>
            <input id="dropDownListItem" class="form-control" title="Filtro por ítem" style="width: 100%;">
        </div>
        <div class="col-4 col-sm-3">
            <label for="dropDownListTeam">Equipo</label>
            <input id="dropDownListTeam" class="form-control" title="Filtro por equipo" style="width: 100%;">
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
                    <h4 class="card-title">Tareas por lugar
                    </h4>
                </div>
                <div class="card-content">
                    <div class="card-body" style="position: relative;">
                        <input id="idparentspot" class="form-control" hidden>
                        <div id="gridSpotTickets" style="width:100%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-12">
            <div class="card" style="margin-bottom: 1rem !important;">
                <div class="card-header">
                    <h4 class="card-title">Tareas por ítem
                    </h4>
                </div>
                <div class="card-content">
                    <div class="card-body" style="position: relative;">
                        <input id="idparentitem" class="form-control" hidden>
                        <div id="gridItemTickets" style="width:100%"></div>
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
<script src="{{ asset('js/reports/report-task.js') }}"></script>
<script src="{{ asset('daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('js/reports/utils.js') }}"></script>
@endsection