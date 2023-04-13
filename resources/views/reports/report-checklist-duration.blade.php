@extends('layouts/contentLayoutMaster')

@section('icon')
<i class="fad fa-clock" style="color:#FD7E14"></i>
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
        <div class="col-4 col-sm-5">
            <label for="dropDownListChecklist">Checklist</label>
            <input id="dropDownListChecklist" class="form-control" title="Filtro por checklist" style="width: 100%;">
        </div>
        <div class="col-4 col-sm-2">
            <button id="btnRefresh" type="button" class="btn btn-danger mt-2"><i class="fad fa-sync"></i> Actualizar datos</button>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-12">
            <div class="card" style="margin-bottom: 1rem !important;">

                <div class="card-content">
                    <div class="card-body" style="position: relative;">
                        <div id="gridChecklistDuration" style="width:100%"></div>
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
<script src="{{ asset('js/reports/report-checklist-duration.js') }}"></script>
<script src="{{ asset('daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('js/reports/utils.js') }}"></script>
@endsection