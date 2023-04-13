<div class="modal fade" id="modalLog" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary white">
                <h4 class="modal-title" id="myModalLabel16">{{__('locale.Log') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="filterLog" style="margin-bottom: 5px;"></div>
                <div id="gridLog"></div>
            </div>
        </div>
    </div>
</div>

<style type="text/css">
    .user-photo {
        display: inline-block;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background-size: 22px 22px;
        background-position: center center;
        vertical-align: middle;
        line-height: 22px;
        box-shadow: inset 0 0 1px #999, inset 0 0 10px rgba(0, 0, 0, .2);

    }

    .user-name {
        display: inline-block;
        vertical-align: middle;
        line-height: 32px;
        padding-left: 3px;
    }

    .ticket-reference {
        text-decoration: underline #007bff;
        font-weight: 500;
        color: #007bff;
        cursor: pointer;
    }
</style>