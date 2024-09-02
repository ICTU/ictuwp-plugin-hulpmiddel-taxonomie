<?php
/**
 * Custom Taxonomy: Hulpmiddel
 * -  hierarchical (like 'category')
 *
 * @package GebruikerCentraalTheme
 *
 * @see https://developer.wordpress.org/reference/functions/register_taxonomy/
 * @see https://developer.wordpress.org/reference/functions/get_taxonomy_labels/
 *
 * CONTENTS:
 * - Set GC_HULPMIDDEL_TAX taxonomy labels
 * - Set GC_HULPMIDDEL_TAX taxonomy arguments
 * - Register GC_HULPMIDDEL_TAX taxonomy
 * - public function fn_ictu_hulpmiddel_get_post_hulpmiddel_terms() - Retreive Hulpmiddel terms with custom field data for Post
 * ----------------------------------------------------- */


if ( ! taxonomy_exists( GC_HULPMIDDEL_TAX ) ) {

	// [1] Hulpmiddel Taxonomy Labels
	$hulpmiddel_tax_labels = [
		'name'                       => _x( 'Hulpmiddel', 'Custom taxonomy labels definition', 'gctheme' ),
		'singular_name'              => _x( 'Hulpmiddel', 'Custom taxonomy labels definition', 'gctheme' ),
		'search_items'               => _x( 'Zoek hulpmiddelen', 'Custom taxonomy labels definition', 'gctheme' ),
		'popular_items'              => _x( 'Populaire hulpmiddelen', 'Custom taxonomy labels definition', 'gctheme' ),
		'all_items'                  => _x( 'Alle hulpmiddelen', 'Custom taxonomy labels definition', 'gctheme' ),
		'edit_item'                  => _x( 'Bewerk hulpmiddel', 'Custom taxonomy labels definition', 'gctheme' ),
		'view_item'                  => _x( 'Bekijk hulpmiddel', 'Custom taxonomy labels definition', 'gctheme' ),
		'update_item'                => _x( 'Hulpmiddel bijwerken', 'Custom taxonomy labels definition', 'gctheme' ),
		'add_new_item'               => _x( 'Voeg nieuw hulpmiddel toe', 'Custom taxonomy labels definition', 'gctheme' ),
		'new_item_name'              => _x( 'Nieuwe hulpmiddel', 'Custom taxonomy labels definition', 'gctheme' ),
		'separate_items_with_commas' => _x( 'Kommagescheiden hulpmiddelen', 'Custom taxonomy labels definition', 'gctheme' ),
		'add_or_remove_items'        => _x( 'Hulpmiddelen toevoegen of verwijderen', 'Custom taxonomy labels definition', 'gctheme' ),
		'choose_from_most_used'      => _x( 'Kies uit de meest-gekozen hulpmiddelen', 'Custom taxonomy labels definition', 'gctheme' ),
		'not_found'                  => _x( 'Geen hulpmiddelen gevonden', 'Custom taxonomy labels definition', 'gctheme' ),
		'no_terms'                   => _x( 'Geen hulpmiddelen gevonden', 'Custom taxonomy labels definition', 'gctheme' ),
		'items_list_navigation'      => _x( 'Navigatie door hulpmiddelenlijst', 'Custom taxonomy labels definition', 'gctheme' ),
		'items_list'                 => _x( 'Hulpmiddelenlijst', 'Custom taxonomy labels definition', 'gctheme' ),
		'item_link'                  => _x( 'Hulpmiddel Link', 'Custom taxonomy labels definition', 'gctheme' ),
		'item_link_description'      => _x( 'Een link naar een Hulpmiddel', 'Custom taxonomy labels definition', 'gctheme' ),
		'menu_name'                  => _x( 'Hulpmiddelen', 'Custom taxonomy labels definition', 'gctheme' ),
		'back_to_items'              => _x( 'Terug naar hulpmiddelen', 'Custom taxonomy labels definition', 'gctheme' ),
		'not_found_in_trash'         => _x( 'Geen hulpmiddelen gevonden in de prullenbak', 'Custom taxonomy labels definition', 'gctheme' ),
		'featured_image'             => _x( 'Uitgelichte afbeelding', 'Custom taxonomy labels definition', 'gctheme' ),
		'archives'                   => _x( 'Hulpmiddel overzicht', 'Custom taxonomy labels definition', 'gctheme' ),
	];

	// [2] Hulpmiddel Taxonomy Arguments
	$hulpmiddel_slug = GC_HULPMIDDEL_TAX;
	// TODO: discuss if slug should be set to a page with the overview template
	// like so:
	// $hulpmiddel_slug = fn_ictu_hulpmiddel_get_hulpmiddel_overview_page();

	$hulpmiddel_tax_args = [
		"labels"             => $hulpmiddel_tax_labels,
		"label"              => _x( 'Hulpmiddelen', 'Custom taxonomy arguments definition', 'gctheme' ),
		"description"        => _x( 'Hulpmiddelen op het gebied van een gebruikersvriendelijke overheid', 'Custom taxonomy arguments definition', 'gctheme' ),
		"hierarchical"       => true,
		"public"             => true,
		"show_ui"            => true,
		"show_in_menu"       => true,
		"show_in_nav_menus"  => false,
		"query_var"          => false,
		// Needed for tax to appear in Gutenberg editor.
		'show_in_rest'       => true,
		"show_admin_column"  => true,
		// Needed for tax to appear in Gutenberg editor.
		"rewrite"            => [
			'slug'       => $hulpmiddel_slug,
			'with_front' => true,
		],
		"show_in_quick_edit" => true,
	];

	// register the taxonomy with these post types
	// 'post',
	// 'page',
	// 'podcast',
	// 'session',
	// 'keynote',
	// 'speaker',
	// 'event',
	// 'video_page',
	$post_types_with_hulpmiddel = array(
		'page',
	);

	// Commented: not needed with only `page` as post type
	// check if the post types exist
	// $post_types_with_hulpmiddel = array_filter( $post_types_with_hulpmiddel, 'post_type_exists' );

	// [3] Register our Custom Taxonomy
	register_taxonomy( GC_HULPMIDDEL_TAX, $post_types_with_hulpmiddel, $hulpmiddel_tax_args );

}


/**
 * fn_ictu_hulpmiddel_get_hulpmiddel_terms()
 *
 * 'Hulpmiddel' is a custom taxonomy (category)
 * It has some extra ACF fields:
 * - hulpmiddel_taxonomy_page: landingspage
 * - hulpmiddel_taxonomy_colorscheme: colorscheme
 * - hulpmiddel_taxonomy_visual: visual (image)
 * - hulpmiddel_taxonomy_link: link to [external] hulpmiddel (URL) [optional]
 *
 * This function fills an array of all
 * terms, with their extra fields...
 *
 * If one $hulpmiddel_name is passed it returns only that
 * If $term_args is passed it uses that for the query
 *
 * @see https://developer.wordpress.org/reference/functions/get_terms/
 * @see https://www.advancedcustomfields.com/resources/adding-fields-taxonomy-term/
 * @see https://developer.wordpress.org/reference/classes/wp_term_query/__construct/
 *
 * @param String $hulpmiddel_name Specific term name/slug to query
 * @param Array $hulpmiddel_args Specific term query Arguments to use
 * @param Boolean $skip_landingspage_when_linked Go straight to Link and bypass Page, even when set?
 */


function fn_ictu_hulpmiddel_get_hulpmiddel_terms( $hulpmiddel_name = null, $term_args = null, $skip_landingspage_when_linked = false ) {

	// TODO: I foresee that editors will want to have a custom order to the taxonomy terms
	// but for now the terms are ordered alphabetically
	$hulpmiddel_taxonomy = GC_HULPMIDDEL_TAX;
	$hulpmiddel_terms    = array();
	$hulpmiddel_query    = is_array( $term_args ) ? $term_args : array(
		'taxonomy'   => $hulpmiddel_taxonomy,
		// We also want Terms with NO linked content, in this case
		'hide_empty' => false,
		'orderby'    => 'name',
		'order'      => 'ASC',
	);

	// NOTE:
	// We want to order the Hulpmiddelen alphabetically
	// based on their name. But we use the linked Page Title
	// for the actual display. So really, we expect to order
	// based on Term -> Page -> Title
	// This is why we need to re-order the array
	// in template-overview-hulpmiddelen.php

	// Query specific term name
	if ( ! empty( $hulpmiddel_name ) ) {
		// If we find a Space, or an Uppercase letter, we assume `name`
		// otherwise we use `slug`
		$RE_disqualify_slug                  = "/[\sA-Z]/";
		$query_prop_type                     = preg_match( $RE_disqualify_slug, $hulpmiddel_name ) ? 'name' : 'slug';
		$hulpmiddel_query[ $query_prop_type ] = $hulpmiddel_name;
	}

	$found_hulpmiddel_terms = get_terms( $hulpmiddel_query );

	if ( is_array( $found_hulpmiddel_terms ) && ! empty( $found_hulpmiddel_terms ) ) {
		// Add our custom Fields to each found WP_Term instance
		// And add to $hulpmiddel_terms[]
		foreach ( $found_hulpmiddel_terms as $hulpmiddel_term ) {
			$hulpmiddel_term_fields = get_fields( $hulpmiddel_term );
			if ( is_array( $hulpmiddel_term_fields ) ) {
				foreach ( $hulpmiddel_term_fields as $key => $val ) {

					// Add path to image url
					if ( $key == 'hulpmiddel_taxonomy_visual' ){
						// Value could be `none`: if so skip
						if ( $val == 'none' ) {
							continue;
						}
						// Optionally convert to img tag with:
						//   '<img width="800" height="450" src="%s/%s" class="hulpmiddel-taxonomy-visual" alt="" decoding="async" loading="lazy" />',
						// for now just return the path:
						$val = sprintf( '%s/%s', GC_HULPMIDDEL_TAX_VISUALS_PATH, $val );
					}

					// Add extra `url` property to Term if we have a linked Page
					if ( $key == 'hulpmiddel_taxonomy_page' && ! empty( $val ) ) {
						$hulpmiddel_term->url = get_permalink( $val );
					}

					// Add extra `direct` property to Term if we have a Link
					// and we want to skip the Page (if set)
					if ( $key == 'hulpmiddel_taxonomy_link' && ! empty( $val ) && $skip_landingspage_when_linked ) {
						$hulpmiddel_term->direct = true;
					}

					$hulpmiddel_term->$key = $val;
				}
			}
			$hulpmiddel_terms[] = $hulpmiddel_term;
		}
	}

	return $hulpmiddel_terms;
}

/**
 * fn_ictu_hulpmiddel_get_post_hulpmiddel_terms()
 *
 * This function fills an array of all
 * terms, with their extra fields _for a specific Post_...
 *
 * - Only top-lever Terms
 * - 1 by default
 *
 * used in [themes]/ictuwp-theme-gc2020/includes/gc-fill-context-with-acf-fields.php
 *
 * @param String|Number $post_id Post to retrieve linked terms for
 *
 * @return Array        Array of WPTerm Objects with extra ACF fields
 */
function fn_ictu_hulpmiddel_get_post_hulpmiddel_terms( $post_id = null, $term_number = 1 ) {
	$return_terms = array();
	if ( ! $post_id ) {
		return $return_terms;
	}

	$post_hulpmiddel_terms = wp_get_post_terms( $post_id, GC_HULPMIDDEL_TAX, [
		'taxonomy'   => GC_HULPMIDDEL_TAX,
		'number'     => $term_number, // Return max $term_number Terms
		'hide_empty' => true,
		'parent'     => 0,
		'fields'     => 'names' // Only return names (to use in `fn_ictu_hulpmiddel_get_hulpmiddel_terms()`)
	] );
	if ( ! empty( $post_hulpmiddel_terms ) && ! is_wp_error( $post_hulpmiddel_terms ) ) {

		$return_terms['title'] = _n( 'Hoort bij het hulpmiddel', 'Hoort bij de hulpmiddelen', count( $post_hulpmiddel_terms ), 'gctheme' ) ;
		$return_terms['items'] = array();

		foreach ( $post_hulpmiddel_terms as $_term ) {
			$full_post_hulpmiddel_term = fn_ictu_hulpmiddel_get_hulpmiddel_terms( $_term );
			if ( ! empty( $full_post_hulpmiddel_term ) ) {
				$return_terms['items'][] = $full_post_hulpmiddel_term[0];
			}
		}

	}

	return $return_terms;
}
