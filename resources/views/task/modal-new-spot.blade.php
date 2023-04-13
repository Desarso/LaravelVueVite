<div id="modalNewSpot" class="modal fade text-left" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nuevo Spot</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formNewSpot">
                    <div class="modal-body">
                        <div class="form-group">
                            <label data-toggle="tooltip" data-placement="top" title="Escriba el nombre">Nombre: </label>
                            <input id="spot-name" name="name" type="text" placeholder="Nombre" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="dropDownListSpotParent" data-toggle="tooltip" data-placement="top"
                                title="Elija el Spot Padre">Spot Padre:</label>
                            <select id="dropDownListSpotParent" name="idparent" class="form-control"></select>
                        </div>
                        <div class="form-group">
                            <label for="dropDownListSpotType" data-toggle="tooltip" data-placement="top"
                                title="Elija el Tipo de Spot">Tipo de Spot:</label>
                            <select id="dropDownListSpotType" name="idtype" class="form-control"></select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="btnNewSpot" type="button" class="btn btn-primary">Agregar</button>
                <button id="btnSpinnerSpot" style="display: none;" class="btn btn-primary waves-effect waves-light" type="button" disabled="">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Agregando...
                </button>
            </div>
        </div>
    </div>
</div>