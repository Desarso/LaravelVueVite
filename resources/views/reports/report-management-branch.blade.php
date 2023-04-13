@extends('layouts/contentLayoutMaster')

@section('icon')
<i class="fad fa-chart-bar" style="color: #FD7E14"></i>
@endsection

@section('title', 'Reporte gestión de sedes')

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
        <input name="group" id="group" hidden />
        <div class="col-6 col-sm-6">
            <label for="dropDownListBranch">Sede</label>
            <select id="dropDownListBranch" class="form-control" name="dropDownListBranch" style="width: 100%;"></select>
        </div>
        <div class="col-6 col-sm-6">
            <label for="dropDownListChecklist">Checklist</label>
            <select id="dropDownListChecklist" class="form-control" title="Filtro por checklist" style="width: 100%;"></select>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-sm-12">
            <div id="gridChecklistManagement" style=""></div>
        </div>
    </div>

    <div class="row mt-1">
        <div class="col-6 col-sm-6">
            <div id="gridChecklistGroup" style="width:100%;"></div>
        </div>
        <div class="col-6 col-sm-6">
            <div id="gridChecklistOption" style="width:100%;"></div>
        </div>
    </div>

</div>
@endsection

@section('page-script')
{{-- Page js files --}}
<script src="{{ asset('js/reports/utils.js') }}"></script>
<script src="{{ asset('js/reports/report-management-branch.js') }}"></script>
<script src="{{ asset('daterangepicker/daterangepicker.js') }}"></script>
@endsection