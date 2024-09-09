<?php
/**
 * ACF fields for the Hulpmiddel Taxonomy Detail page template
 */
if ( ! function_exists( 'acf_add_local_field_group' ) ) {
	return;
}

// Remove default Hulpmiddel Taxonomy metabox from side
// of Hulpmiddel Taxonomy Detail pages
// (remove_meta_box() does not work in GB) so we need JS:
add_action( 'admin_enqueue_scripts', 'fn_ictu_hulpmiddel_admin_scripts' );

function fn_ictu_hulpmiddel_admin_scripts() {
	global $post;
	if ( $post ) {
		// Do we have a post of whatever kind at hand?
		// Get template name; this will only work for pages, obviously
		$page_template = get_post_meta( $post->ID, '_wp_page_template', true );

		if ( ( GC_HULPMIDDEL_TAX_OVERVIEW_TEMPLATE === $page_template ) || ( GC_HULPMIDDEL_TAX_DETAIL_TEMPLATE === $page_template ) ) {
			// Enqueue GB JS that hides the Hulpmiddel Taxonomy side panel
			wp_enqueue_script( 'gc-hulpmiddel-editor', GC_HULPMIDDEL_TAX_ASSETS_PATH . '/scripts/gc-hulpmiddel-editor.js' );
		}
	}
}

// Add Custom ACF MetaBox for coupling a Hulpmiddel Term to a Page

acf_add_local_field_group( array(
	'key' => 'group_66cca9209f62c',
	'title' => 'Metabox: selecteer hulpmiddel',
	'fields' => array(
		array(
			'key' => 'field_66cca921173b2',
			'label' => 'Selecteer het hulpmiddel voor deze pagina',
			'name' => 'hulpmiddel_detail_select_hulpmiddel_term',
			'aria-label' => '',
			'type' => 'taxonomy',
			'instructions' => 'Als je deze pagina straks bekijkt, zul je zien dat de kleuren en visual worden overgenomen vanuit de instellingen van het hulpmiddel die je kiest.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'taxonomy' => GC_HULPMIDDEL_TAX,
			'add_term' => 0,
			'save_terms' => 0,
			'load_terms' => 0,
			'return_format' => 'id',
			'field_type' => 'radio',
			'allow_null' => 0,
			'multiple' => 0,
			'bidirectional' => 1,
			'bidirectional_target' => array(
				0 => 'field_66cca443ef1fe',
			),
			'ui' => 1,
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'page_template',
				'operator' => '==',
				'value' => 'template-detail-hulpmiddelen.php',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => true,
	'description' => '',
	'show_in_rest' => 0,
) );
