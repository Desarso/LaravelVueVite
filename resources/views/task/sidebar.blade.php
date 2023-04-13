<style>
    .pre-scrollable {
        max-height: 900px !important;
    }
</style>

<section id="data-list-view" class="data-list-view-header">
    <div class="add-new-data-sidebar pre-scrollable">
        <div class="overlay-bg"></div>
        <div class="add-new-data">
            <div id="createTaskTitle" class="div pt-2 px-2 d-flex new-data-title justify-content-between">

                <h4 class="text-uppercase" style="font-weight: bold">
                    <i id="createTaskIcon"></i>
                    <span id="_createTaskTitle">{{ __('locale.Add Task') }}</span>
                </h4>
                <small id="createTaskShortDescription"> </small>
                <div class="hide-data-sidebar">
                    <i class="feather icon-x"></i>
                </div>
            </div>
            <div class="data-items pb-3 pre-scrollable">
                <div class="data-fields px-2 mt-1">
                    <form id="formTicket" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-sm-12">
                                <p id="label-team" class="text-bold-500 mb-75" title="Equipo"></p>
                            </div>
                            <div class="col-sm-12" style="margin-bottom: 15px">
                                <label for="multiSelectItem"><strong>{{ __('locale.TASK') }}</strong></label>
                                <select id="multiSelectItem" name="iditem"></select>
                            </div>
                            <fieldset id="sidebarfields" class="col-sm-12" style="padding-left:0px;">

                                <div class="col-sm-12" id="spots">
                                    <label for="multiSelectSpot" data-toggle="tooltip" data-placement="top"
                                        title="">{{ __('locale.Spot') }}</label>
                                    <select id="multiSelectSpot" name="spots"></select>
                                    <button id="btn-tree-spots" type="button" class="btn btn-icon btn-flat-primary waves-effect waves-light" style="{{ auth()->user()->isadmin ? '' : 'display:none;' }}"><i class="fad fa-sitemap"></i></button>
                                </div>

                                <div class="col-sm-12 data-field-col" data-template="description">
                                    <label for="description" data-toggle="tooltip" data-placement="top"
                                        title="">{{ __('locale.Description') }}</label>
                                    <textarea class="form-control" id="description" name="description" rows="4"
                                        placeholder="Description"></textarea>
                                </div>
                                <div class="col-sm-12 data-field-col">
                                    <div class="demo-section k-content">
                                        <label for="duedate">Fecha de vencimiento</label>
                                        <input id="duedate" name="duedate" title="Fecha de vencimiento" style="width: 100%;" />
                                    </div>
                                </div>
                                <div class="col-sm-12 data-field-col hidden" data-template="idasset">
                                    <label for="multiSelectAsset" data-toggle="tooltip" data-placement="top"
                                        title="">{{ __('locale.Asset') }}</label>
                                    <select id="multiSelectAsset" name="idasset"></select>
                                </div>
                                <div id="divUsers" class="col-sm-12 data-field-col" data-template="users" style="display:none;">
                                    <label for="multiSelectUser" data-toggle="tooltip" data-placement="top"
                                        title="">{{ __('locale.Responsible') }}</label>
                                    <select id="multiSelectUser" name="users"></select>
                                </div>
                                <div class="col-sm-12 data-field-col" data-template="byclient">
                                    <input type="checkbox" class="k-checkbox" id="byresource">
                                    <label class="k-checkbox-label" for="byresource" data-toggle="tooltip"
                                        data-placement="top" title="">Crear tarea para cada recurso</label>
                                </div>
                                <div id="divCopies" class="col-sm-12 data-field-col" data-template="copies" style="display:none;">
                                    <label for="multiSelectCopy" data-toggle="tooltip" data-placement="top"
                                        title="">{{ __('locale.CC') }}</label>
                                    <select id="multiSelectCopy" name="copies"></select>
                                </div>

                                <div class="col-sm-12 data-field-col" data-template="priority">
                                    <label for="multiSelectPriority" data-toggle="tooltip" data-placement="top"
                                        title="">{{ __('locale.Priority') }}</label>
                                    <select id="multiSelectPriority" name="idpriority"></select>
                                </div>
                                <div class="col-sm-12 data-field-col" data-template="byclient">
                                    <input type="checkbox" class="k-checkbox" id="byclient">
                                    <label class="k-checkbox-label" for="byclient" data-toggle="tooltip"
                                        data-placement="top" title="">{{ __('locale.Reported by Client') }}</label>
                                </div>
                                <div class="col-sm-12 data-field-col" data-template="tags">
                                    <label for="multiSelectTag" data-toggle="tooltip" data-placement="top"
                                        title="">{{ __('locale.Tags') }}</label>
                                    <select id="multiSelectTag" name="tags"></select>
                                </div>

                                <!--  Hidden default fields -->
                                <div class="col-sm-12 data-field-col" data-template="justification">
                                    <label for="justification" data-toggle="tooltip" data-placement="top"
                                        title="">{{ __('locale.Justification') }}</label>
                                    <textarea class="form-control" id="justification" name="justification" rows="4"
                                        placeholder="{{ __('locale.Justification') }}"></textarea>
                                </div>
                                <div class="col-sm-12 data-field-col hidden" data-template="code">
                                    <label for="code" data-toggle="tooltip" data-placement="top"
                                        title="">{{ __('locale.Code') }}</label>
                                    <input type="text" id="code" name="code" class="form-control"></select>
                                </div>
                                <div class="col-sm-12 data-field-col hidden" data-template="quantity">
                                    <label for="quantity" data-toggle="tooltip" data-placement="top"
                                        title="">{{ __('locale.Quantity') }}</label>
                                    <input type="number" name="quantity" class="form-control"></select>
                                </div>
                                <div class="col-sm-12 data-field-col hidden" data-template="approvers">
                                    <label for="multiSelectApprovers" data-toggle="tooltip" data-placement="top"
                                        title="">{{ __('locale.Approvers') }}</label>
                                    <select id="multiSelectApprovers" name="approvers"></select>
                                </div>
                            </fieldset>
                            <fieldset style="margin-top: 10px;">
                                <div id="imagesContainer" class="col-sm-12">
                                </div>
                            </fieldset>
                        </div>
                    </form>
                </div>
            </div>
            <div class="add-data-footer d-flex justify-content-around mt-1">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-8 col-sm-6">
                                <div class="add-data-btn">
                                    <button class="btn btn-primary" id="btnCreateTicket">Crear</button>
                                    <button class="btn btn-primary" id="btnUpdateTicket">Editar</button>
                                </div>
                            </div>
                            <div class="col-4 col-sm-6">
                                <div class="cancel-data-btn">
                                    <input type="reset" class="btn btn-outline-danger" value="Cancelar">
                                </div>
                            </div>
                        </div>
                        <small id="lbl-created-by" class="mt-2" style="font-weight:100;"></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>