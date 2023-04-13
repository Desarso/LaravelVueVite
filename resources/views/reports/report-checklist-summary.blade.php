@extends('layouts.contentLayoutMaster')

@section('icon')
<i class="fad fa-chart-bar" style="color: #FD7E14"></i>
@endsection

@section('title', 'Resumen de Checklist')

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

@section('page-style')
<link rel="stylesheet" href="{{ asset(mix('css/pages/card-analytics.css')) }}">
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
        <div class="col-4 col-sm-3 pl-0">
            <label for="dropDownListSpot">Sede</label>
            <input id="dropDownListSpot" class="form-control" title="Filtro por sede" style="width: 100%;">
        </div>
        <div class="col-4 col-sm-3">
            <label for="dropDownListChecklist">Checklist</label>
            <input id="dropDownListChecklist" class="form-control" title="Filtro por checklist" style="width: 100%;">
        </div>
    </div>

    <div class="row">
        <div class="card" style="width: 100%; height: 700px;">
            <div class="card-content">
                <div class="card-body" style="display: inline-flex; width: 100%;">
                    <div id="grid-checklist-summary"></div>
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
    <script src="{{ asset('js/reports/report-checklist-summary.js') }}"></script>
    <script src="{{ asset('daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('js/reports/utils.js') }}"></script>
    @endsection