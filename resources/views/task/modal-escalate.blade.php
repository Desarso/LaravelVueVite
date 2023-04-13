<div id="modalEscalate" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="title-modal-escalate" class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="dropDownListTeamEscalate" data-toggle="tooltip" data-placement="top"
                        title="Equipo">Equipo</label>
                    <select id="dropDownListTeamEscalate" name="idteam" style="width: 100%;"></select>
                </div>
                <div class="form-group">
                    <label for="multiSelectEscalateUser" data-toggle="tooltip" data-placement="top"
                        title="">Responsables</label>
                    <select id="multiSelectEscalateUser" name="spots"></select>
                </div>
                <div class="alert alert-primary" role="alert">
                    <h4 class="alert-heading">Información</h4>
                    <p class="mb-0">
                        Los responsables actuales de la tarea pasarán a ser copiados.
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button id="btn-escalate" type="button" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>