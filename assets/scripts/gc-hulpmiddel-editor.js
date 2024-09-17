/**
 * Hulpmiddel specific editor functions
 */

if ( wp ) {
    wp.domReady( () => {
        // Remove Hulpmiddel Taxonomy panel from sidebar
        // console.log(`gc-hulpmiddel-editor.js`);
        wp.data.dispatch( 'core/editor').removeEditorPanel( 'taxonomy-panel-hulpmiddel' );
        // Check:
        // wp.data.dispatch( 'core/editor').toggleEditorPanelEnabled( 'taxonomy-panel-hulpmiddel' );
        // wp.data.select( 'core/editor' ).getCurrentPost().template
    } );
}
