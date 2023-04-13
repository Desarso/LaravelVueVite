<div class="modal fade" id="modalNote" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success white">
                <h5 class="modal-title" id="title-modal-note"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 0%;">
                <ul id="context-menu">
                    <li id="deleteNote">Eliminar</li>
                </ul>
                <div class="card chat-widget" style="margin-bottom:0%;">
                    <div class="chat-app-window">
                        <div class="user-chats">
                            <div class="chats">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="text" class="form-control mr-1 ml-50" id="note" placeholder="Escribe una nota...">
                <button type="button" id="btnCreateNote" class="btn btn-success send waves-effect waves-light"><i
                        class="fa fa-paper-plane-o d-lg-none"></i> <span class="d-none d-lg-block">Send</span></button>
            </div>
        </div>
    </div>
</div>