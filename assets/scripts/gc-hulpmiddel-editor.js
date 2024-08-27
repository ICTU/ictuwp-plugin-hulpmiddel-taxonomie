/**
 * Hulpmiddel specific editor functions
 */

if ( wp ) {
    wp.domReady( () => {
        // Remove Hulpmiddel Taxonomy panel from sidebar
        // on pages that have the Hulpmiddel Detail Page template
        wp.data.dispatch( 'core/editor').removeEditorPanel( 'taxonomy-panel-hulpmiddel' );
    } );
}
