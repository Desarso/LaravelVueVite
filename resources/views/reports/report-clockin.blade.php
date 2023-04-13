@extends('layouts/contentLayoutMaster')

@section('icon')
<i class="fad fa-clipboard-user"></i>
@endsection

@section('title', 'Reporte de Clockin')

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
@include('reports.modal-overtime')
<!--modal-->
<div id="modal-approval" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title-modal-checklist">Aprobación de Horas Trabajadas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col mb-2">
                        <label>Regular</label>
                        <input id="regular_time" type="text" class="form-control" disabled>
                    </div>
                    <div class="col">
                        <label>Extra</label>
                        <input id="overtime" type="text" class="form-control" disabled>
                    </div>
                    <div class="col">
                        <label>Doble</label>
                        <input id="double_time" type="text" class="form-control" disabled>
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-2">
                        <input id="regular_time_approve" type="time" class="form-control">
                    </div>
                    <div class="col">
                        <input id="overtime_approve" type="time" class="form-control">
                    </div>
                    <div class="col">
                        <input id="double_time_approve" type="time" class="form-control">
                    </div>
                </div>
                <label for="note">Comentario:</label>
                <fieldset class="form-group">
                    <textarea class="form-control" id="note" rows="3" placeholder="Escribe un comentario..."></textarea>
                </fieldset>
            </div>
            <div class="modal-footer">
                <button id="btn-verified" type="button" data-status="VERIFIED" class="btn btn-success btn-md btn-block waves-effect waves-light btn-status">Verificar</button>
            </div>
        </div>
    </div>
</div>
<!--modal-->

<div class="container-fluid">
    <div class="row mb-1">
        <div class="col-4 col-sm-4">
            <label for="dropDownListUser">Usuario</label>
            <input id="dropDownListUser" class="form-control" title="Filtro por usuario" style="width: 100%;">
        </div>
        <div class="col-4 col-sm-4">
            <label for="dropDownListStatus">Estado</label>
            <input id="dropDownListStatus" class="form-control" title="Filtro por estado" style="width: 100%;">
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-12">
            <div class="card" style="margin-bottom: 1rem !important;">
                <div class="card-header">
                    <h4 class="card-title">Lista de usuarios
                    </h4>
                </div>
                <div class="card-content">
                    <div class="card-body" style="position: relative;">
                        <div id="gridClockin" style="width:100%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('page-script')
<script src="{{ asset('js/reports/report-clockin.js') }}"></script>
<script src="{{ asset('daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('js/reports/utils.js') }}"></script>
@endsection