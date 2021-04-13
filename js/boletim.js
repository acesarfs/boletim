(function($){
    $( document ).ready(function() {
        /*$( '#sortable' ).sortable();
        $( '#sortable' ).disableSelection();
        $( "#sortable" ).on( "sortchange", function( event, ui ) {
            console.log('Mudança');
            $("input[name*='nides']").val('2,3');
        } );
*/
        $( '#sortable tbody' ).sortable();
        $( '#sortable tbody' ).disableSelection();
    });
})(jQuery);

