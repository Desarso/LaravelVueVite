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

<!--modal-->
<style>
    .label-tag {
        font-weight: 500;
        font-size: 20px;
    }

    .label-value {
        font-weight: 300;
        font-size: 17px;
    }
</style>

<div id="modal-overtime" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title-modal-checklist">Detalles de horas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="height: 550px;">
                <div style="margin: 15px;">
                    <div class="row">
                        <div class="col-6">
                            <label class="label-tag" >Usuario: </label>
                            <label id="label-person-name" class="label-value"></label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <label class="label-tag">Fecha: </label>
                            <label id="label-date-overtime" class="label-value"></label>
                        </div>
                    </div>
                </div>
                <div id="gridClockinDetails" style="width: 96%; height: 95%; position: absolute;"></div>
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
            <label for="dropDownListBranch">Site</label>
            <input id="dropDownListBranch" class="form-control" title="Filtro por sitio" style="width: 100%;">
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
<script src="{{ asset('js/reports/report-clockin-time.js') }}"></script>
<script src="{{ asset('daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('js/reports/utils.js') }}"></script>
@endsection