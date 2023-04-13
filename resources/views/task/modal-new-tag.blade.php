<div id="modalNewTag" class="modal fade text-left" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nueva Etiqueta</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formNewTag">
                    <div class="modal-body">
                        <div class="form-group">
                            <label data-toggle="tooltip" data-placement="top" title="Escriba el nombre">Nombre: </label>
                            <input id="tag-name" name="name" type="text" placeholder="Nombre" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="tagColorPalette" data-toggle="tooltip" data-placement="top" title="Elija un Color">Color</label>
                            <div class="col-sm-10">
                                <div id="tagColorPalette" name="color"></div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="btnNewTag" type="button" class="btn btn-primary">Agregar</button>
                <button id="btnSpinnerTag" style="display:none;" class="btn btn-primary waves-effect waves-light"
                    type="button" disabled="">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Agregando...
                </button>
            </div>
        </div>
    </div>
</div>