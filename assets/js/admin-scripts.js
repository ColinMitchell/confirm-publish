jQuery( function( $ ) {

    /**
     * Adds confirmation to publish event for posts
     * @type {Element}
     */
    $( '#publish' ).not( '.disabled' ).click( function( e ) {

        // Setup Vars
        var $this = $( this );
        var result = false;
        var $publishButton = $( '#publish' );

        // Prevent default action from happening
        e.preventDefault();

        // Determine type of confirmation to show if any
        if( $this.val() === "Publish" ) {
            result = confirm( confirmPublish.strings.publish );
        } else if( $this.val() === "Update" && confirmPublish.current_post.post_status === "publish" && $('#post-status-display').text().trim() === "Published" ) {
            result = confirm( confirmPublish.strings.update );
        } else {
            result = true;
        }

        // If confirm is true, unbind event and trigger click on the publish button.
        if( result ) {
            $publishButton.unbind();
            $publishButton.trigger( 'click' );
        } else {
            return false;
        }

    } );

} );