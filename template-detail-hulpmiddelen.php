<?php
/**
 * Template Name: [Hulpmiddel] detailpagina
 *
 * @package    WordPress
 * @subpackage Timber v2
 */

$timber_post = Timber::get_post();

$current_hulpmiddel_taxid = get_current_hulpmiddel_tax();
$current_hulpmiddel_term  = get_term_by( 'id', $current_hulpmiddel_taxid, GC_HULPMIDDEL_TAX );

$context                = Timber::context();
$context['post']        = $timber_post;
$context['modifier']    = 'hulpmiddel-detail';
$context['is_unboxed']  = true;
$context['show_author'] = false;

if ( $current_hulpmiddel_term && ! is_wp_error( $current_hulpmiddel_term ) ) {
	// Update body class
	$context['body_class'] = ( $context['body_class'] ?: '' ) . ' single-hulpmiddel hulpmiddel--' . $current_hulpmiddel_term->slug;
}

$templates = [ 'hulpmiddel-detail.twig', 'page.twig' ];

/**
 * returns the ID for the hulpmiddel term that is
 * attached to this page in ACF field 'hulpmiddel_detail_select_hulpmiddel_term'
 *
 * @return int
 */
function get_current_hulpmiddel_tax() {
	global $post;

	$term_id = get_field( 'hulpmiddel_detail_select_hulpmiddel_term' ) ?: 0;
	if ( ! $term_id ) {
		$aargh = _x( 'Geen hulpmiddel gekoppeld aan deze pagina. ', 'Hulpmiddel taxonomy error message', 'gctheme' );
		if ( current_user_can( 'editor' ) ) {
			$editlink = get_edit_post_link( $post );
			$aargh    .= '<a href="' . $editlink . '">' . _x( 'Kies een relevante hulpmiddel voor deze pagina. ', 'Hulpmiddel taxonomy error message', 'gctheme' ) . '</a>';
		}
		die( $aargh );
	}

	return $term_id;
}

/**
 * Fill Timber $context with available page/post Blocks/Metaboxes
 * @see /includes/gc-fill-context-with-acf-fields.php
 */
if ( function_exists( 'gc_fill_context_with_acf_fields' ) ) {
	$context = gc_fill_context_with_acf_fields( $context );
}

// We have a valid Hulpmiddel Term for this page
if ( $current_hulpmiddel_term && ! is_wp_error( $current_hulpmiddel_term ) ) {

	// Get custom ACF fields for this WP_Term..
	// filter out 'empty' or nullish values
	$current_hulpmiddel_term_fields = array_filter(
		get_fields( $current_hulpmiddel_term ) ?: [],
		function ( $field ) {
			return ! empty( $field );
		}
	);

	if ( $current_hulpmiddel_term_fields ) {
		// We have some custom ACF fields for this Term

		// If we have a palette:
		if ( isset( $current_hulpmiddel_term_fields['hulpmiddel_taxonomy_colorscheme'] ) ) {
			// .. store it in $context
			$context['palette'] = $current_hulpmiddel_term_fields['hulpmiddel_taxonomy_colorscheme'];
			// .. update body class
			$context['body_class'] = ( $context['body_class'] ?: '' ) . ' palette--' . $context['palette'];
		}

		// If we have a visual, store it in $context
		if ( isset( $current_hulpmiddel_term_fields['hulpmiddel_taxonomy_visual'] ) ) {
			// Store complete Path to image (if available)
			$context['visual'] = sprintf( '%s/%s', GC_HULPMIDDEL_TAX_VISUALS_PATH, $current_hulpmiddel_term_fields['hulpmiddel_taxonomy_visual'] );
		}

		// If we have an extra Hulpmiddel Link
		if ( isset( $current_hulpmiddel_term_fields['hulpmiddel_taxonomy_link'] ) ) {
			$context['hulpmiddel_link'] = $current_hulpmiddel_term_fields['hulpmiddel_taxonomy_link'];
		}
	}

	// Fallback: Term VISUAL
	// if ( ! array_key_exists( 'visual', $context ) ) {
	// 	$context['visual'] = sprintf( '%s/', GC_HULPMIDDEL_TAX_VISUALS_PATH ) . 'default.svg';
	// }

	// // Fallback: Term COLORSCHEME
	// if ( ! array_key_exists( 'palette', $context ) && function_exists( 'gc_get_colorschemes' ) ) {
	// 	$available_color_themes = gc_get_colorschemes();
	// 	$context['palette'] = $available_color_themes ? reset( $available_color_themes ) : 'green';
	// }

	// CONTENT
	// -----------------------------

	// text for 'inleiding' is taken from term description
	// -----------------------------
	// $timber_post->post_content = $current_hulpmiddel_term->description;
	// Use Excerpt instead?! Fall back to Term description if no Excerpt is set?
	if ( empty( $context['intro'] ) ) {
		$context['intro'] = wpautop( $current_hulpmiddel_term->description );
	}

	/**
	 *  (10) Intro Text (Metabox)
	 * ----------------------------- */
	$metabox_intro_text = get_field( 'metabox_intro_text' );
	if ( ! empty( $metabox_intro_text ) ) {
		$context['metabox_intro_text'] = $metabox_intro_text;
	}

	// /**
	//  *  (20) Infoblokken (USP)
	//  * ----------------------------- */
	// $metabox_fields = get_field( 'icoonblokken' );
	// if ( ! empty( $metabox_fields ) ) {
	// 	if ( $metabox_fields && 'ja' === $metabox_fields['metabox_icoonblokken_show_or_not'] ) {
	// 		$metabox_fields_items = $metabox_fields['metabox_icoonblokken_items'];

	// 		foreach ( $metabox_fields_items as $block ) {

	// 			$item = array();
	// 			if ( isset( $block['title'] ) && $block['title'] && isset( $block['description'] ) && $block['description'] ) {
	// 				$item['modifier']                            = $block['icon'];
	// 				$item['title']                              = $block['title'];
	// 				$item['content']                            = wpautop( $block['description'] );
	// 				$context['metabox_icoonblokken']['items'][] = $item;
	// 			}

	// 		}

	// 	}
	// }


	// /**
	//  *  (30) Events box
	//  * ----------------------------- */
	// // Only show events if Events Manager plugin is active
	// if ( class_exists( 'EM_Events' ) ) {
	// 	// the events manager is active, which helps for selecting events

	// 	$metabox_fields = get_field( 'events' );

	// 	if ( $metabox_fields && 'ja' === $metabox_fields['metabox_events_show_or_not'] ) {

	// 		$method           = $metabox_fields['metabox_events_selection_method'] ?? '';
	// 		$maxnr            = $metabox_fields['metabox_events_max_nr'] ?? 3;
	// 		$metabox_item_ids = array();

	// 		if ( 'manual' === $method ) {
	// 			// manually selected events, returns an array of events
	// 			$metabox_item_ids = $metabox_fields['metabox_events_selection_manual'];

	// 		} else {
	// 			// select latest events for $current_hulpmiddel_taxid
	// 			// _event_start_date is a meta field for the events post type
	// 			// this query selects future events for the $current_hulpmiddel_taxid
	// 			$currentdate = date( "Y-m-d" );
	// 			$args        = array(
	// 				'posts_per_page' => $maxnr,
	// 				'post_type'      => EM_POST_TYPE_EVENT,
	// 				'meta_key'       => '_event_start_date',
	// 				'orderby'        => 'meta_value',
	// 				'post_status'    => 'publish',
	// 				'order'          => 'ASC', // order by start date ascending
	// 				'fields'         => 'ids', // only return IDs
	// 				'tax_query'      => array(
	// 					array(
	// 						'taxonomy' => GC_HULPMIDDEL_TAX,
	// 						'field'    => 'term_id',
	// 						'terms'    => $current_hulpmiddel_term->term_id,
	// 					)
	// 				),
	// 				'meta_query'     => array(
	// 					array(
	// 						'key'     => '_event_start_date',
	// 						'value'   => $currentdate,
	// 						'compare' => '>=',
	// 						'type'    => 'DATE',
	// 					),
	// 				)
	// 			);
	// 			$query_items = new WP_Query( $args );

	// 			if ( $query_items->have_posts() ) {
	// 				// we only use post ids for the $metabox_item_ids array
	// 				$metabox_item_ids = $query_items->posts;
	// 			}

	// 			// ensure to reset the main query to original main query
	// 			wp_reset_query();
	// 		}

	// 		if ( $metabox_item_ids ) {
	// 			// we have events
	// 			$context['metabox_events']          = [];
	// 			$context['metabox_events']['items'] = [];
	// 			$context['metabox_events']['title'] = $metabox_fields['metabox_events_titel'] ?? '';
	// 			$context['metabox_events']['descr'] = $metabox_fields['metabox_events_description'] ?? '';
	// 			$url                                = $metabox_fields['metabox_events_url_overview'] ?? [];

	// 			// Add CTA 'overzichtslink' as cta Array to metabox_events
	// 			if ( $url && isset( $url['title'] ) && isset( $url['url'] ) ) {
	// 				$context['metabox_events']['cta']          = [];
	// 				$context['metabox_events']['cta']['title'] = $url['title'];
	// 				$context['metabox_events']['cta']['url']   = $url['url'];
	// 			}

	// 			foreach ( $metabox_item_ids as $post_id ) {

	// 				$item                                 = prepare_card_content( get_post( $post_id ) );
	// 				$context['metabox_events']['items'][] = $item;
	// 			}
	// 			$context['metabox_events']['columncounter'] = count( $context['metabox_events']['items'] );
	// 		}
	// 	}

	// }

	/**
	 * (40) Posts box
	 * ----------------------------- */
	$metabox_fields = get_field( 'berichten' );

	if ( $metabox_fields && 'ja' === $metabox_fields['metabox_posts_show_or_not'] ) {

		$method           = $metabox_fields['metabox_posts_selection_method'] ?? '';
		$maxnr            = 3; // todo TBD: should this be a user editable field?
		$metabox_item_ids = array();

		if ( 'manual' === $method ) {
			// manually selected events, returns an array of posts
			$metabox_item_ids = $metabox_fields['metabox_posts_selection_manual'];

		} else {

			// Do we have a Post Category?
			if ( array_key_exists( 'metabox_posts_category', $metabox_fields ) ) {
				$metabox_posts_category = $metabox_fields['metabox_posts_category'];
			}

			$args = array(
				'posts_per_page' => $maxnr,
				'post_type'      => 'post',
				'post_status'    => 'publish',
				'fields'         => 'ids', // only return IDs
				'tax_query'      => array(
					array(
						'taxonomy' => GC_HULPMIDDEL_TAX,
						'field'    => 'term_id',
						'terms'    => $current_hulpmiddel_term->term_id,
					)
				)
			);

			// If we have a post category, add it to the Tax query
			// we do not only query on Hulpmiddel Tax, but on Post Category as well
			if ( ! empty( $metabox_posts_category ) ) {
				$args['tax_query']['relation'] = 'AND';
				$args['tax_query'][] = array(
					'taxonomy' => 'category',
					'field'    => 'term_id',
					'terms'    => $metabox_posts_category,
				);
			}

			$query_items = new WP_Query( $args );
			if ( $query_items->have_posts() ) {
				// we only use post ids for the $metabox_item_ids array
				$metabox_item_ids = $query_items->posts;
			}

			// ensure to reset the main query to original main query
			wp_reset_query();
		}

		if ( $metabox_item_ids ) {

			$context['metabox_posts']                = [];
			$context['metabox_posts']['items']       = [];
			$context['metabox_posts']['cta']         = [];
			$context['metabox_posts']['title']       = $metabox_fields['metabox_posts_titel'] ?? '';
			$context['metabox_posts']['description'] = $metabox_fields['metabox_posts_description'] ?? '';
			$archive_link_method                     = $metabox_fields['metabox_posts_archive_selection'] ?? [];

			// $archive_link_method can be:
			// none : Geen link
			// automatic : Automatische link naar taxonomie archief
			// custom : Geheel eigen link
			if ( ! empty( $archive_link_method ) && $archive_link_method !== 'none' ) {
				// Automatic Archive Link:
				if ( 'automatic' === $archive_link_method ) {
					// Based on Taxonomy (default category, but now Hulpmiddel), so check if it's not empty..
					// NOTE: user could ALSO have selected a Post Category
					// ..but we ignore this for the archive link and use the Hulpmiddel archive here...
					if ( isset( $current_hulpmiddel_term ) && ! empty( $current_hulpmiddel_term->term_id ) ) {

						$metabox_posts_category_name = $current_hulpmiddel_term->name;
						$metabox_posts_category_text = $metabox_fields['metabox_posts_archive_selection_automatic_link_text'] ?: _x( 'Bekijk alle berichten', 'Linktekst voor Hulpmiddel berichten', 'gctheme' );
						$metabox_posts_category_link = get_term_link( (int) $current_hulpmiddel_term->term_id, GC_HULPMIDDEL_TAX );

						// `$metabox_posts_category_link` is the default Hulpmiddel Taxonomy archive link
						// but we want to link to a special 'Archive' page if it's available.
						// So it's a fallback.

						// So:
						// We try and get the 1st page with the template-posts-hulpmiddelen.php template
						// that is a child of the current hulpmiddel page and use this page permalink as the archive link.

						// Query all page ID's with the template-posts-hulpmiddelen.php template:
						// and check if they are either:
						// - a child of this hulpmiddel page
						// - or have a link to this hulpmiddel term
						$all_child_archive_pages       = array();
						$all_child_archive_pages_query = new WP_Query( array(
							'post_type' => 'page',
							'post_status' => 'publish',
							'post_parent' => $timber_post->ID,
							'posts_per_page' => 1,
							'fields' => 'ids',
							'meta_query' => array(
								array(
									'key' => '_wp_page_template',
									'value' => GC_HULPMIDDEL_TAX_POSTS_ARCHIVE_TEMPLATE,
									// "relation" => "AND",
									// 'compare' => '=',
								),
							)
						) );

						// The custom Loop
						if ( $all_child_archive_pages_query->have_posts() ) {
							while ( $all_child_archive_pages_query->have_posts() ) {
								// Let's take what we need, here the whole object but you can pick only what you need:
								$all_child_archive_pages_query->the_post();
								$all_child_archive_pages[] = $post;
							}

							// Override `$metabox_posts_category_link`
							// with our found page permalink.
							$metabox_posts_category_link = get_permalink( $all_child_archive_pages[0] );

							// Reset our postdata:
							wp_reset_postdata();

							// Replace placeholder with term name in automatic link text
							$metabox_posts_category_text = sprintf( $metabox_posts_category_text, $metabox_posts_category_name );

							$context['metabox_posts']['cta']['title'] = $metabox_posts_category_text;
							$context['metabox_posts']['cta']['url']   = $metabox_posts_category_link;

						} else {
							// We have NO archive page
							// So do NOT fill $context['metabox_posts']['cta']
						}
					}
				}

				// Custom Archive Link:
				if ( 'custom' === $archive_link_method ) {
					$custom_archive_link = $metabox_fields['metabox_posts_archive_selection_custom_link'];
					if ( ! empty( $custom_archive_link ) ) {
						$context['metabox_posts']['cta']['title'] = $custom_archive_link['title'] ?: _x( 'Bekijk alle berichten', 'Linktekst voor Hulpmiddel berichten', 'gctheme' );
						$context['metabox_posts']['cta']['url']   = $custom_archive_link['url'];
					}
				}
			}

			foreach ( $metabox_item_ids as $post_id ) {

				$item  = prepare_card_content( get_post( $post_id ) );
				$image = get_the_post_thumbnail_url( $post_id, IMAGESIZE_16x9 );
				if ( $image ) {
					// decorative image, no value for alt attr.
					$item['img'] = '<img src="' . $image . '" alt="" />';
					// Provide Image as URL instead of HTML?
					// $item['img']     = $image;
					// $item['img_alt'] = '';
				}
				$context['metabox_posts']['items'][] = $item;
			}
			$context['metabox_posts']['columncounter'] = count( $context['metabox_posts']['items'] );
		}
	}

	/**
	 * (50) profiles box
	 * ----------------------------- */
	// name of field is 'group_profiles', not 'profiles', as to avoid possible conflicts with function 'gc_get_profiles'
	// (located at [theme]]/includes/post_types/profile-post-type.php )
	$metabox_profiles = get_field( 'group_profiles' );

	if ( $metabox_profiles && 'ja' === $metabox_profiles['metabox_profiles_show_or_not'] ) {

		$context['metabox_profiles']          = [];
		$context['metabox_profiles']['title'] = $metabox_profiles['metabox_profiles_title'] ?: _x( 'Meer weten over dit hulpmiddel?', 'default title for profiles', 'gctheme' );

		if ( function_exists( 'gc_get_profiles' ) ) {
			$array_profiles = array();
			foreach ( $metabox_profiles['metabox_profiles_linked_profiles'] as $profile ) {
				// pass on the profile in rather specific date structure as required by gc_get_profiles
				$item                  = array();
				$item['profile']       = array();
				$item['profile'][]     = $profile;
				$item['profile_label'] = '';
				$array_profiles[]      = $item;
			}
			$context['metabox_profiles']['profiles'] = gc_get_profiles( array( 'profiles' => $array_profiles ) );

		}

	}

	/**
	 * (60) Contact form box
	 * (actually it's a Gravity Forms form)
	 * ----------------------------- */
	$metabox_contactform_id = get_field( 'metabox_contactform_id' );
	if ( $metabox_contactform_id ) {
		$gravityforms_block = '<!-- wp:gravityforms/form {"formId":"' . $metabox_contactform_id . '"} /-->';
		$parsed_blocks      = do_blocks( $gravityforms_block );

		if ( $parsed_blocks ) {
			$context['metabox_contactform'] = $parsed_blocks;
		}
	}

	/**
	 * (70) Partners / Logo's
	 * (we render the gc/logos Block in Theme's hulpmiddel-detail.twig)
	 * ----------------------------- */
	// Require a filled in list for this section to show
	$metabox_logos_list  = get_field( 'logos_list' );
	if ( $metabox_logos_list ) {
		$context['metabox_logos'] = array(
			'title'       => get_field( 'logos_title' ),
			'description' => get_field( 'logos_description' ),
			'list'        => $metabox_logos_list,
		);
	}

}


Timber::render( $templates, $context );
