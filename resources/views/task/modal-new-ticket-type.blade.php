<div id="modalNewTicketType" class="modal fade text-left" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nuevo Tipo de Tarea</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formNewTicketType">
                    <div class="modal-body">
                        <div class="form-group">
                            <label data-toggle="tooltip" data-placement="top" title="Escriba el nombre">Nombre: </label>
                            <input id="ticket-type-name" name="name" type="text" placeholder="Nombre" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="dropDownListTeam" data-toggle="tooltip" data-placement="top" title="Elija el Equipo">Equipo:</label>
                            <select id="dropDownListTeam" name="idteam" class="form-control"></select>
                        </div>
                        <div class="form-group">
                            <label for="picker" data-toggle="tooltip" data-placement="top" title="Elija el color">Color:</label>
                            <input id="picker"/>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="btnNewTicketType" type="button" class="btn btn-primary">Agregar</button>
                <button id="btnSpinnerTicketType" style="display: none;" class="btn btn-primary waves-effect waves-light" type="button" disabled="">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Agregando...
                </button>
            </div>
        </div>
    </div>
</div>