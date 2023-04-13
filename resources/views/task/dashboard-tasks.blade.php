@extends('layouts/contentLayoutMaster')

@section('title', __('locale.' . 'Dashboard'))

@section('vendor-style')

    <!-- vendor css files -->
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/charts/apexcharts.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/tether-theme-arrows.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/tether.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/shepherd-theme-default.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/swiper.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/charts/apexcharts.css')) }}">

@endsection
@section('page-style')

    <!-- Page css files -->
    <link rel="stylesheet" href="{{ asset(mix('css/pages/dashboard-analytics.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/pages/card-analytics.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/plugins/tour/tour.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/pages/app-chat.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/pages/data-list-view.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/plugins/extensions/swiper.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/pages/card-analytics.css')) }}">

    <!-- Date Range Picker -->
    <link rel="stylesheet" href="{{ asset('daterangepicker/daterangepicker.css') }}">

    <style>
        .filter-select{
            width: 100%;
        }
        .k-grid td {
            border-color: transparent;
        }

        .k-filter-operator {
            display: none !important;
        }

        .blink {
            animation: blinker 2s linear infinite;
            color: #ea5455 !important;
            font: bold !important;
            font-weight: 900;
            width: 200px;
            padding-left: 3px;
        }

        @keyframes blinker {
            50% {
                opacity: 0;
            }
        }
        .btn-remove-filter{
            transition: transform 0.5s;
        }
        .btn-remove-filter:hover{
            transform: scale(1.5)
        }
    </style>

@endsection
@section('content')
    @include('task.sidebar')
    @include('task.modal-status')
    @include('task.modal-note')
    @include('task.modal-log')
    @include('task.modal-verify')
    @include('task.modal-checklist')
    @include('task.modal-new-item')
    @include('task.modal-new-spot')
    @include('task.modal-new-ticket-type')
    @include('task.modal-new-tag')
    @include('task.modal-protocol')
    @include('task.modal-signature')
    @include('task.modal-file')
    @include('task.modal-duration')
    @include('task.modal-escalate')
    @include('task.modal-filter')

    <script id="noDataTemplate" type="text/x-kendo-tmpl">
        # var value = instance.input.val(); #
        # var id = instance.element[0].id; #
        <div>
            No hay resultados con - '#: value #' ?
        </div>
        <br />
        <button class="k-button" onclick="addNewConfig('#: id #', '#: value #')">Agregar</button>
</script>

    <section id="dashboard-analytics">

        <div class="row">
            <div class="col-12">
                <section id="component-swiper-centered-slides-2">
                    <div class="swiper-centered-slides-2 swiper-container p-1" style="padding-top: 0rem !important;">
                        <div id="swiper-ticket-type" class="swiper-wrapper">
                            @foreach ($global_ticket_types as $type)
                                <div data-idtype="{{ $type->value }}"
                                    class="card-ticket-type swiper-slide rounded swiper-shadow py-1 px-3 d-flex">
                                    <i class="{{ $type->icon }} mr-50 font-medium-3"></i>
                                    <div class="swiper-text">{{ $type->text }}</div>
                                </div>
                            @endforeach
                            <!--
                                <div id="card-new-ticket-type" class="swiper-slide rounded swiper-shadow py-1 px-3 d-flex">
                                    <i class="fa fa-plus-circle mr-50 font-medium-3"></i>
                                    <div class="swiper-text">{{ __('locale.New') }}</div>
                                </div>
                                -->
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 col-sm-12">
                <div class="card">
                    <div class="card-header" style="padding-bottom:0px !important; padding-top:10px !important;">
                        <!--<h4 class="card-title">Estadísticas</h4>-->
                        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                        <div class="heading">
                            <ul class="list-inline mb-0">
                                <li>
                                    @if (property_exists(json_decode(Auth::user()->preferences), 'dashboardCollapse') &&
                                        json_decode(Auth::user()->preferences)->dashboardCollapse)
                                        <a id="btn-collapse" data-action="collapse"><i
                                                class="feather icon-chevron-down"></i></a>
                                    @else
                                        <a id="btn-collapse" data-action="collapse" class="rotate"><i
                                                class="feather icon-chevron-down"></i></a>
                                    @endif
                                </li>
                            </ul>
                        </div>
                    </div>
                    @if (property_exists(json_decode(Auth::user()->preferences), 'dashboardCollapse') &&
                        json_decode(Auth::user()->preferences)->dashboardCollapse)
                        <div class="card-content collapse">
                        @else
                            <div class="card-content collapse show">
                    @endif
                    <div class="card-body" style="padding-bottom: 0px;padding-top: 0px;">
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="card">
                                    <div class="card-header text-center justify-content-between pt-0 pb-0"
                                        style="display:inline">
                                        <h4 class="card-title text-bold-700">{{ __('locale.Dashboard') }}</h4>
                                    </div>
                                    <div class="card-content">
                                        <div class="card-body" style="padding-bottom: 0px;padding-top: 0px;">
                                            <div class="row">
                                                <div
                                                    class="col-lg-6 col-12 d-flex justify-content-between flex-column order-lg-1 order-2 mt-lg-0 mt-2">
                                                    <div>
                                                        <div id="efficacy-chart" class=""></div>
                                                    </div>
                                                </div>
                                                <div
                                                    class="col-lg-6 col-12 d-flex justify-content-between flex-column text-left order-lg-2 order-1 mt-1">
                                                    <div class="">
                                                        <p style="position: absolute;cursor: pointer;"
                                                            class="mb-0 ticket-stat" data-idstatus="1">
                                                            {{ __('locale.Pending') }}</p>
                                                        <p id="count-pendint" class="text-right mb-0">0</p>
                                                        <div class="progress progress-bar-danger mt-25">
                                                            <div id="bar-pendint" class="progress-bar" role="progressbar"
                                                                aria-valuenow="50" aria-valuemin="50" aria-valuemax="100"
                                                                style="width:0%"></div>
                                                        </div>
                                                    </div>
                                                    <div class="">
                                                        <p style="position: absolute;cursor: pointer;"
                                                            class="mb-0 ticket-stat" data-idstatus="2">
                                                            {{ __('locale.In Progress') }}</p>
                                                        <p id="count-progress" class="text-right mb-0">0</p>
                                                        <div class="progress progress-bar-success mt-25">
                                                            <div id="bar-progress" class="progress-bar"
                                                                role="progressbar" aria-valuenow="60" aria-valuemin="60"
                                                                aria-valuemax="100" style="width:0%">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="">
                                                        <p style="position: absolute;cursor: pointer;"
                                                            class="mb-0 ticket-stat" data-idstatus="3">
                                                            {{ __('locale.Paused') }}</p>
                                                        <p id="count-paused" class="text-right mb-0">0</p>
                                                        <div class="progress progress-bar-warning mt-25">
                                                            <div id="bar-paused" class="progress-bar" role="progressbar"
                                                                aria-valuenow="70" aria-valuemin="70" aria-valuemax="100"
                                                                style="width:0%"></div>
                                                        </div>
                                                    </div>
                                                    <div class="">
                                                        <p style="position: absolute;cursor: pointer;"
                                                            class="mb-0 ticket-stat" data-idstatus="4">
                                                            {{ __('locale.Finished') }}</p>
                                                        <p id="count-finished" class="text-right mb-0">0</p>
                                                        <div class="progress progress-bar-secondary mt-25">
                                                            <div id="bar-finished" class="progress-bar"
                                                                role="progressbar" aria-valuenow="90" aria-valuemin="90"
                                                                aria-valuemax="100" style="width:0%">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr style="margin-bottom:0.5rem; margin-top:0px;" />
                                            <div class="row">
                                                <div
                                                    class="col-6 d-flex align-items-between text-center flex-column border-right">
                                                    <p class="mb-50">{{ __('locale.Total Tasks') }}</p>
                                                    <p id="total-task" class="font-large-1 text-bold-700">0</p>
                                                </div>
                                                <div class="col-6 d-flex align-items-between text-center flex-column">
                                                    <p class="mb-50">{{ __('locale.Average Duration') }}</p>
                                                    <p id="average-duration" class="font-large-1 text-bold-700">0 min
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="card">
                                    <div class="card-header text-center justify-content-between pt-0 pb-0"
                                        style="display:inline">
                                        <h4 class="card-title">{{ __('locale.My Tasks') }}</h4>
                                    </div>
                                    <div class="card-content">
                                        <div class="card-body" style="padding-bottom: 0px;padding-top: 0px;">
                                            <div class="row">
                                                <div class="col-sm-6 col-12 d-flex justify-content-center"
                                                    style="height:200px;">
                                                    <div id="my-efficiency-chart"></div>
                                                </div>
                                                <div class="col-sm-6 col-12 d-flex justify-content-center"
                                                    style="height:200px;">
                                                    <div id="my-efficacy-chart"></div>
                                                </div>
                                            </div>
                                            <hr style="margin-bottom:0.5rem; margin-top:0px;" />
                                            <div class="row">
                                                <div
                                                    class="col-3 d-flex align-items-between text-center flex-column border-right">
                                                    <p class="mb-50">{{ __('locale.Pending') }}</p>
                                                    <p id="my-count-pendint" class="font-large-1 text-bold-700">0</p>
                                                </div>
                                                <div
                                                    class="col-3 d-flex align-items-between text-center flex-column border-right">
                                                    <p class="mb-50">{{ __('locale.Finished') }}</p>
                                                    <p id="my-count-finished" class="font-large-1 text-bold-700">0</p>
                                                </div>
                                                <div
                                                    class="col-3 d-flex align-items-between text-center flex-column border-right">
                                                    <p class="mb-50">{{ __('locale.Failed') }}</p>
                                                    <p id="my-count-reproved" class="font-large-1 text-bold-700">0</p>
                                                </div>
                                                <div class="col-3 d-flex align-items-between text-center flex-column">
                                                    <p class="mb-50">{{ __('locale.Delayed') }}</p>
                                                    <p id="my-count-expired" class="font-large-1 text-bold-700">0</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
        <div class="row">
            <div class="bar-menu-table" style="width: 25%">
                <div class="form-label-group has-icon-left">
                    <input type="text" class="form-control" id="search" placeholder="{{ __('locale.Search') }}">
                    <div class="form-control-position">
                        <i class="feather icon-search"></i>
                    </div>
                    <label for="iconLabelLeft">{{ __('locale.Search') }}</label>
                </div>
            </div>

            <div class="order-elements">
            <div id="div-filters-applied" class="alert alert-primary alert-dismissible" role="alert" style="display: none; height: 40px; margin-top: 15px; margin-right:50px;">
                <div class="div-filters-applied">
                    <p class="mb-0">
                        <i class="far fa-filter"></i> <strong id="message-filters-applied"></strong> 
                    </p>
                </div>

                <button id="btn-close-filter-applied" type="button" class="close">
                  <span ><i class="feather icon-x-circle"></i></span>
                </button>
              </div>
              <!--
                <div class="bar-menu-table" style="padding: 0px !important">
                    <div class="filter-select">
                        <div id="filter-selected" class="filter-select bar-menu-table" style="display: none;">
                            <p class="mt-1 mb-0" title="Filtro seleccionado" style="display: none;">
                            </p>
                        </div>
                    </div>
                </div>
                -->
                <div class="bar-menu-table">
                    <div class="form-label-group has-icon-left">

                        <button type="button" id="newFilter" class="btn btn-primary plus-filter" data-toggle="modal"
                            data-target="#modal-filter">
                            <i class="fas fa-plus"></i>
                        </button>
                        <x-filter.filter-list :filters="$filters" />
                    </div>
                </div>
                <div class="bar-menu-table">
                    <div class="form-label-group has-icon-left">
                        <div id="dateRangePicker">
                            <i class="fa fa-calendar"></i>&nbsp;
                            <span></span> <i class="fa fa-caret-down"></i>
                        </div>
                    </div>
                </div>
                <div class="bar-menu-table">
                    <div class="form-label-group has-icon-left">
                        <button id="btn-excel" type="button"
                            class="btn btn-icon btn-success mr-1 waves-effect waves-light" data-toggle="tooltip"
                            data-placement="top" title="" data-original-title="Exportar Excel"><i
                                class="fas fa-file-excel"></i></button>
                    </div>
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card" style="overflow: auto">
                    <div class="card-content table-responsive">
                        <ul id="contextMenuTicket"></ul>
                        <div class="animategrid" id="gridTicket"></div>
                    </div>
                </div>
            </div>
        </div>


    </section>
    <!-- Dashboard Task end -->
@endsection

@section('vendor-script')
    <!-- vendor files -->
    <script src="{{ asset(mix('vendors/js/charts/apexcharts.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/tether.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/shepherd.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/swiper.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/charts/apexcharts.min.js')) }}"></script>
@endsection

@section('page-script')
    <script>
        window.hierarchySpots = {!! $hierarchySpots !!};
        window.ticketsFilter = {!! $ticketsFilter !!};
    </script>
    <!-- Page js files -->
    <script src="{{ asset(mix('js/ticket.js')) }}"></script>
    <script src="{{ asset(mix('js/scripts/extensions/swiper.js')) }}"></script>

    <!-- Date Range Picker -->
    <script src="{{ asset('daterangepicker/daterangepicker.js') }}"></script>
@endsection
