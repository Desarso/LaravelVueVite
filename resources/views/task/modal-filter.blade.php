    <!-- Modal -->
    <style>
        .bar-menu-table {
            height: 50px;
            padding: 5px;
            margin: 10px;
        }

        #dateRangePicker {
            cursor: pointer;
            padding: 8px 10px;
            border: 1px solid #c1c1c1;
            min-width: 160px;
            width: auto;
            height: 38px;
            margin-top: 0px;
            border-radius: 5px;
            background: #babfc7;
            color: rgb(255 255 255);
            text-align: center;
        }

        .modal-filter .k-filter-container {
            display: contents;
        }

        .k-filter-container {
            display: none;
        }

        #filterTicket,
        .k-filter-toolbar,
        .k-toolbar {
            width: 100%;
            background: #fff;
        }

        .k-filter .k-filter-toolbar .k-toolbar {
            border-style: none;
        }

        .k-filter .k-filter-lines {
            padding-left: 0;
            padding-right: 1.5rem;
        }

        .k-toolbar .k-filter-toolbar-item:nth-child(2) button:first-of-type {
            position: absolute;
            top: 5px;
            right: 5px;
            width: 200px;
            color: #33a596;
            content: "Agregar filtro +" !important;
        }

        .k-toolbar .k-filter-toolbar-item:nth-child(2) button:first-of-type {
            content: "Agregarfiltro" !important;
        }

        .k-toolbar .k-filter-toolbar-item:nth-child(3) {
            display: none;
        }

        .modal-filter .k-toolbar .k-filter-toolbar-item:nth-child(2) button span::before {
            position: absolute;
            top: -12px;
            right: -15px;
            background: none;
            content: "+";
            width: 10px;
            height: 40px;
            font-size: 2rem;
            color: #33a596;
            font-weight: 900;
            border-radius: 5px;
            line-height: 1.2;
        }

        .modal-filter .k-toolbar .k-filter-toolbar-item:nth-child(3) button span::before {
            position: absolute;
            top: -9px;
            right: -12px;
            background: #eb3a57;
            content: "X";
            width: 34px;
            height: 34px;
            font-size: 20px;
            color: #fff;
            border-radius: 5px;
            line-height: 1.4;
        }

        .modal-filter .k-toolbar .k-filter-toolbar-item:nth-child(1) div:first-of-type {
            position: absolute;
            top: 5px;
            left: 5px;
        }

        .modal-filter .k-toolbar .k-filter-toolbar-item:nth-child(1) .k-button-group .k-button {
            width: 100px;
            margin-right: 10px;
        }

        .order-elements {
            display: flex;
            justify-content: flex-end;
            width: 73%;
        }

        #filterList {
            border-top-left-radius: 0px;
            border-bottom-left-radius: 0px;
        }

        .plus-filter {
            padding: 12px;
            top: 0px;
            left: -22px;
            position: absolute;
            border-top-right-radius: 0px;
            border-bottom-right-radius: 0px;
            background: #92969d !important;
        }

        .plus-filter:hover {
            box-shadow: -1px 1px 3px 0px #92969d;
        }

        .k-filter .k-filter-lines .k-filter-item:last-child>.k-filter-toolbar::after,
        .k-filter .k-filter-toolbar::before,
        .k-filter .k-filter-item::before {
            content: none;
        }

        #btnNewFilter {
            position: absolute;
            right: 139px;
            bottom: -75px;
        }

        .k-toolbar {
            display: flex;
            flex-wrap: nowrap;
            flex-direction: row;
            justify-content: center;
        }

        .modal-filter {
            min-width: 650px;
            max-width: 700px;
            height: auto;
        }

        .k-filter-lines .k-filter-item .k-filter-toolbar .k-toolbar .k-filter-toolbar-item:nth-child(4) {
            background: none;
            color: red;
            border-radius: 5px;
            padding: 2px;
        }

        .k-toolbar .k-filter-toolbar-item:nth-child(4) button span {
            color: #fff;
        }

        .k-textbox {
            width: 18em;
        }

        .k-dropdown .k-dropdown-wrap {
            border-color: #e4e7eb;
        }

        .item-edit-filter:hover,
        .btn-delete-filter:hover,
        .btn-delete-all-filter:hover {
            color: #19191a
        }

        #btnNewFilter button {
            margin-right: 9px;
        }

        .k-filter-toolbar .k-toolbar .k-filter-toolbar-item:nth-child(4) button {
            display: none;
        }

        .k-filter-group-main .k-filter-lines .k-filter-item .k-filter-toolbar .k-toolbar .k-filter-toolbar-item:nth-child(4) button {
            display: inline;
        }

        .k-filter-group-main .k-filter-lines .k-filter-item .k-filter-toolbar .k-toolbar .k-filter-toolbar-item:nth-child(4) button span {
            color: red;
            right: 4px;
        }

        .New-Filter-Save {
            display: flex;
            align-items: center;
        }
    </style>
    <div class="modal fade " id="modal-filter" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-filter">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('locale.Filters') }}</h5>
                    <button type="button" id="closeModal" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="" id="filterTicket"></div>
                    <div>
                        <div class="row">
                            <div class="col text-center">

                                <button type="button" id="btn-show-to-save-filter" style="width: 180px; color: #33a596; display: none;"><span class="k-icon k-i-save mr-1"> </span><span class="k-button-text"> Guardar filtro</span></button>

                                <div id="div-save-filter-section" class="input-group" style="width:64%; margin-left:99px; display:none;">
                                    <input id="txt-filter-name" type="text" class="form-control" placeholder="Escriba el nombre del filtro" aria-describedby="button-addon2">
                                    <div class="input-group-append">
                                        <button id="btn-save-filter" class="btn btn-primary waves-effect waves-light" type="button">Guardar filtro</button>
                                    </div>
                                </div>

                            </div>
                        </div>


                    </div>
                    <x-filter.new-filter />
                </div>
                <div class="modal-footer">
                    <!--
                    <div id="showNewFilterSave">
                    <form class="px-2 py-2">
                        <div class="New-Filter-Save">
                            <div class="Name-New-filter" style="margin: 0px 10px;">
                            <input type="text" class="form-control" id="filterNewName"
                                placeholder="">
                            </div>
                            <button id="btnSavenNewFilter"
                                class="btn btn-success waves-effect waves-light">Guardar</button>
                        </div>
                    </form>
                </div>
    -->

                    <button type="button" class="btn btn-warning waves-effect waves-light btn-block mt-1 mr-2" id="btn-clear-filter"><i class="fas fa-trash-alt"></i> Limpiar filtros</button>
                    <button type="button" class="btn btn-success waves-effect waves-light btn-block mt-1" id="btn-apply-filter"><i class="fa fa-check"></i> Aplicar filtros</button>
                </div>
            </div>
        </div>
    </div>