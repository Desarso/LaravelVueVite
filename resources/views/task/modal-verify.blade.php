<style>
.vs-checkbox-con .vs-checkbox.vs-checkbox-lg {
    width: 33px;
    height: 33px;
}
.vs-checkbox-con input:checked ~ .vs-checkbox.vs-checkbox-lg .vs-checkbox--check .vs-icon {
    font-size: 1.9rem;
}
</style>

<div id="modalVerify" class="modal fade text-left" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="title-modal-verify"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12" style="text-align:center;">
                        <ul class="list-unstyled mb-0">
                            <li class="d-inline-block mr-2">
                                <fieldset>
                                    <div class="vs-checkbox-con vs-checkbox-success">
                                        <input id="approved" type="checkbox">
                                        <span class="vs-checkbox vs-checkbox-lg">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-thumbs-up"></i>
                                            </span>
                                        </span>
                                        <span class="">Aprobado</span>
                                    </div>
                                </fieldset>
                            </li>
                            <li class="d-inline-block mr-2">
                                <fieldset>
                                    <div class="vs-checkbox-con vs-checkbox-danger">
                                        <input id="reprobate" type="checkbox">
                                        <span class="vs-checkbox vs-checkbox-lg">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-thumbs-down"></i>
                                            </span>
                                        </span>
                                        <span class="">Reprobado</span>
                                    </div>
                                </fieldset>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 data-field-col mt-2">
                        <label for="verifyNote" title="Escribe una observación" data-toggle="tooltip" data-placement="top">
                            Descripción
                        </label>
                        <textarea id="verifyNote" name="verifyNote" class="form-control" rows="4">
                        </textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="btnVerifyTask" type="button" class="btn btn-primary">Aceptar</button>
            </div>
        </div>
    </div>
</div>