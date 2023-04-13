@extends('layouts/contentLayoutMaster')

@section('icon')
<i class="fad fa-chart-pie" style="color: #B552E3"></i>
@endsection

@section('title', 'Reporte de Equipos')

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
            <label for="dropDownListSpot">Sede</label>
            <input id="dropDownListSpot" class="form-control" title="Filtro por Sede" style="width: 100%;">
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

    <section id="statistics-card">
        <div class="row">
            
            <div class="col-lg-6 col-sm-6 col-12">
                <div class="card" style="margin-bottom: 1rem !important;">
                    <div class="card-header">
                        <h4 class="card-title">Tareas por equipo</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body" style="padding-top:0px; padding-bottom:0px;">
                            <div id="team-tasks-chart"></div>
                        </div>
                    </div>
                </div>
                
            </div>
            
            <div class="col-md-6 col-12">
            <div class="card" style="margin-bottom: 1rem !important;">
                <div class="card-header">
                    <h4 class="card-title">Estado de  tareas</h4>
                </div>
                <div class="card-content">
                    <div class="card-body" style="padding-top:0px; padding-bottom:0px;">
                        <div id="team-task-statuses-chart"></div>
                    </div>
                </div>
            </div>
        </div>

            <!--
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="card"  >
                    <div class="card-header d-flex align-items-start pb-0">
                        <div>
                            <h2 id="count-pendint" class="text-bold-700 mb-0">0</h2>
                            <p>Pendientes</p>
                        </div>
                        <div class="avatar bg-rgba-danger p-50 m-0">
                            <div class="avatar-content">
                                <i class="feather icon-clock text-danger font-medium-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card"  >
                    <div class="card-header d-flex align-items-start pb-0">
                        <div>
                            <h2 id="count-finished" class="text-bold-700 mb-0">0</h2>
                            <p>Finalizadas</p>
                        </div>
                        <div class="avatar bg-rgba-light p-50 m-0">
                            <div class="avatar-content">
                                <i class="feather icon-alert-octagon text-light font-medium-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="card"  >
                    <div class="card-header d-flex align-items-start pb-0">
                        <div>
                            <h2 id="count-progress" class="text-bold-700 mb-0">4</h2>
                            <p>En progreso</p>
                        </div>
                        <div class="avatar bg-rgba-success p-50 m-0">
                            <div class="avatar-content">
                                <i class="feather icon-play text-success font-medium-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card"  ">
                    <div class="card-header d-flex align-items-start pb-0">
                        <div>
                            <h2 id="count-paused" class="text-bold-700 mb-0">0</h2>
                            <p>Pausadas</p>
                        </div>
                        <div class="avatar bg-rgba-warning p-50 m-0">
                            <div class="avatar-content">
                                <i class="feather icon-pause text-warning font-medium-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            -->
        </div>
    </section> 

 
    <div class="row">
 
        <div class="col-md-6 col-sm-12 col-12">">
            <div class="card" style="margin-bottom: 1rem !important;">
                <div class="card-header">
                    <h4 class="card-title">Actividad</h4>
                </div>
                <div class="card-content">
                    <div class="card-body" style="padding-top:0px; padding-bottom:0px;">
                        <div id="team-activity-chart"></div>
                    </div>
                </div>
            </div>
        </div>
 
    </div>
    <div class="row ">
        <div class="col-md-12 col-12">
            <div class="card" style="margin-bottom: 1rem !important;">
                <div class="card-header">
                    <h4 class="card-title">Lista de tareas
                    </h4>
                </div>
                <div class="card-content">
                    <div class="card-body" style="position: relative;">
                        <div id="gridTickets" style="width:100%"></div>
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
<script src="{{ asset('js/reports/report-teams.js') }}"></script>
<script src="{{ asset('daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('js/reports/utils.js') }}"></script>
@endsection