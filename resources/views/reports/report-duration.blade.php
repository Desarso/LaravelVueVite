@extends('layouts/contentLayoutMaster')

@section('icon')
<i class="fad fa-clipboard-user"></i>
@endsection

@section('title', 'Reporte de Duraciones')

@section('vendor-style')

<!-- vendor css files -->
<link rel="stylesheet" href="{{ asset(mix('vendors/css/charts/apexcharts.css')) }}">
<link rel="stylesheet" href="{{ asset('daterangepicker/daterangepicker.css') }}">

<style>
    div.k-grid-norecords {
        display: block !important;
    }
    .ticket-pendint{
        background-color: rgba(234, 84, 85, 0.15) !important;
    }

    .ticket-progress{
        background-color: rgba(40, 199, 111, 0.15) !important;
    }

    .ticket-paused{
        background-color: rgba(255, 159, 67, 0.15) !important;
    }

    .ticket-finished{
        background-color: rgba(132, 127, 131, 0.15) !important;
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
            <label for="dropDownListItem">Tarea</label>
            <input id="dropDownListItem" class="form-control" title="Filtro por tarea" style="width: 100%;">
        </div>
        <div class="col-4 col-sm-3">
            <label for="dropDownListTeam">Equipo</label>
            <input id="dropDownListTeam" class="form-control" title="Filtro por equipo" style="width: 100%;">
        </div>
        <div class="col-4 col-sm-3">
            <label for="dropDownListUser">Usuario</label>
            <input id="dropDownListUser" class="form-control" title="Filtro por usuario" style="width: 100%;">
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-12">
            <div class="card" style="margin-bottom: 1rem !important;">
                <div class="card-header">
                    <h4 class="card-title">Lista de tareas por lugar
                    </h4>
                </div>
                <div class="card-content">
                    <div class="card-body" style="position: relative;">
                        <div id="gridDurationSpot" style="width:100%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-12">
            <div class="card" style="margin-bottom: 1rem !important;">
                <div class="card-header">
                    <h4 class="card-title">Lista de tareas por usuario
                    </h4>
                </div>
                <div class="card-content">
                    <div class="card-body" style="position: relative;">
                        <div id="gridDuration" style="width:100%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@section('vendor-script')
@endsection

@section('page-script')
<script>
 
</script>
<script src="{{ asset('js/reports/report-duration.js') }}"></script>
<script src="{{ asset('daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('js/reports/utils.js') }}"></script>
@endsection