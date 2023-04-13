@extends('layouts/contentLayoutMaster')

@section('icon')
<i class="fad fa-chart-bar" style="color: #FD7E14"></i>
@endsection

@section('title', 'Reporte por sede')

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

    <div class="row mb-5">
        <input name="group" id="group" hidden />
        <div class="col-4 col-sm-3">
            <label for="dropDownListBranch">Sede</label>
            <select id="dropDownListBranch" class="form-control" name="dropDownListBranch" style="width: 100%;"></select>
        </div>
        <div class="col-4 col-sm-3">
            <label for="dropDownListChecklist">Checklist</label>
            <select id="dropDownListChecklist" class="form-control" title="Filtro por checklist" style="width: 100%;"></select>
        </div>
        <div class="col-4 col-sm-3">
            <label for="dropDownListUser">Responsable</label>
            <select id="dropDownListUser" class="form-control" title="Filtro por responsable" style="width: 100%;"></select>
        </div>
        <div class="col-4 col-sm-3">
            <div class="custom-control custom-switch custom-switch custom-switch-success custom-control-inline ml-2 mt-2" {{ Auth::user()->isadmin ? '' : 'hidden' }}>
              <input type="checkbox" class="custom-control-input" id="showinreport">
              <label class="custom-control-label" for="showinreport">
              </label>
              <span class="switch-label">Filtrar ítems</span>
            </div>
        </div>
    </div>

    <div class="row mb-1">
        <div class="col-4 col-sm-3">
            <div class="col m--align-center">
                <h3 class="m-widget1__title">
                    Cumplimiento
                </h3>
            </div>
            <div class="col m--align-center">
                <span id="lbl-completed" style="font-size:2.5rem;" class="m-widget1__number m--font-danger">
                    %0
                </span>
            </div>
        </div>
        <div class="col-4 col-sm-3">
            <div class="col m--align-center">
                <h3 class="m-widget1__title">
                    Evaluación
                </h3>
            </div>
            <div class="col m--align-center">
                <span id="lbl-evaluation" style="font-size:2.5rem;color:#12c684" class="m-widget1__number">
                    %0
                </span>
            </div>
        </div>
        <div class="col-4 col-sm-3">
            <div class="col m--align-center">
                <h3 class="m-widget1__title">
                    Cumplimiento Evaluación
                </h3>
            </div>
            <div class="col m--align-center">
                <span id="lbl-completed-evaluation" style="font-size:2.5rem;" class="m-widget1__number m--font-primary">
                    %0
                </span>
            </div>
        </div>
    </div>

    <div class="m-portlet">
        <div class="m-portlet__body  m-portlet__body--no-padding">
            <div class="row m-row--no-padding m-row--col-separator-xl">
                <div class="col-xl-12">
                    <div class="m-widget14">
                        <div class="row  align-items-center">
                            <div class="col demo-section k-content wide">
                                <div id="chart-checklist-section" style="height:250px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="gridBranch" style="width: 49%; display: inline-block;"></div>
    <div id="gridOptions" style="width: 49%; display: inline-block;"></div>
</div>
@endsection

@section('page-script')
{{-- Page js files --}}
<script src="{{ asset('js/reports/utils.js') }}"></script>
<script src="{{ asset('js/reports/report-organization.js') }}"></script>
<script src="{{ asset('daterangepicker/daterangepicker.js') }}"></script>
@endsection