var mQuickSidebarTicket = function() {
    var topbarAsideTicket = $('#sidebar-ticket');
    var topbarAsideTicketClose = $('#sidebar_close_ticket');
    var topbarAsideTicketToggle = $('#btnTicket');
    var topbarAsideTicketContent = topbarAsideTicket.find('.m-quick-sidebar__content');

    var initOffcanvas2 = function() {
        topbarAsideTicket.mOffcanvas({
            class: 'm-quick-sidebar2',
            overlay: true,  
            close: topbarAsideTicketClose,
            toggle: topbarAsideTicketToggle
        });   

        // run once on first time dropdown shown
        topbarAsideTicket.mOffcanvas().one('afterShow', function() {
            mApp.block(topbarAsideTicket);

            setTimeout(function() {
                mApp.unblock(topbarAsideTicket);
                
                topbarAsideTicketContent.removeClass('m--hide');
            }, 1000);                         
        });
    }

    return {     
        init: function() {  
            initOffcanvas2(); 
        }
    };
}();

$(document).ready(function() {
    mQuickSidebarTicket.init();
});