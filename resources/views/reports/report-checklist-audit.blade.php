@extends('layouts/contentLayoutMaster')

@section('icon')
<i class="fad fa-chart-bar" style="color: #FD7E14"></i>
@endsection

@section('title', 'Reporte Checklist de Auditoría')

@section('vendor-style')
<!-- vendor css files -->
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
            <label for="dropDownListChecklist">Checklist</label>
            <input id="dropDownListChecklist" class="form-control" title="Filtro por checklist" style="width: 100%;">
        </div>
        <div class="col-4 col-sm-4">
            <label for="dropDownListSpot">Lugar</label>
            <input id="dropDownListSpot" class="form-control" title="Filtro por lugar" style="width: 100%;">
        </div>
        <div class="col-4 col-sm-4">
            <label for="dropDownListUser">Creado por</label>
            <input id="dropDownListUser" class="form-control" title="Filtro por responsable" style="width: 100%;">
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-12">
            <div class="card" style="margin-bottom: 1rem !important;">
                <div class="card-header">
                    <h4 class="card-title">Lista de checklists
                    </h4>
                </div>
                <div class="card-content">
                    <div class="card-body" style="position: relative;">
                        <div id="gridChecklist" style="width:100%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@section('page-script')
{{-- Page js files --}}
<script src="{{ asset('js/reports/report-checklist-audit.js') }}"></script>
<script src="{{ asset('daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('js/reports/utils.js') }}"></script>
@endsection