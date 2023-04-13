<style>
    .label-tag {
        font-weight: 500;
        font-size: 20px;
    }

    .label-value {
        font-weight: 300;
        font-size: 17px;
    }
</style>

<div id="modal-overtime" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title-modal-checklist">Detalles de horas Extra</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="height: 550px;">
                <div style="margin: 15px;">
                    <div class="row">
                        <div class="col-6">
                            <label class="label-tag" >Usuario: </label>
                            <label id="label-person-name" class="label-value"></label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <label class="label-tag">Fecha: </label>
                            <label id="label-date-overtime" class="label-value"></label>
                        </div>
                    </div>
                </div>
                <div id="gridClockinDetails" style="width: 96%; height: 95%; position: absolute;"></div>
            </div>
        </div>
    </div>
</div>