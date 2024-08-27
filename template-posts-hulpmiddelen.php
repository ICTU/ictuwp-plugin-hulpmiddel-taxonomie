<?php
/**
 * Template Name: [Hulpmiddel] artikelen archief
 *
 * @package    WordPress
 * @subpackage Timber v2
 */
global $paged;
if ( ! isset( $paged ) || ! $paged ) {
	$paged = 1;
}

$timber_post                  = Timber::get_post();

$context                      = Timber::context();
$context['post']              = $timber_post;
$context['modifier']           = 'hulpmiddel-post-archive';
$context['is_unboxed']        = true;
$context['has_intro_overlap'] = false; // We only set to true when we have items
$context['show_author']       = false;
$context['title']             = $timber_post->title;

// Use the '00 - Inleiding' `post_inleiding` field as intro text
// when available. If not, we try and add some generic Hulpmiddel info (below)
$context['descr'] = wpautop( get_field( 'post_inleiding' ) );

$templates        = [ 'hulpmiddel-posts-archive.twig', 'archive.twig' ];

// Get current hulpmiddel based on Parent Page
// If no hulpmiddel found, render a default page
if ( ! empty( $timber_post->post_parent ) ) {
    $parent_page_template = get_post_meta( $timber_post->post_parent, '_wp_page_template', true );
    // Try and retrieve the Hulpmiddel tax. Term from parent page.
    if ( $parent_page_template === GC_HULPMIDDEL_TAX_DETAIL_TEMPLATE ) {
        $parent_page_hulpmiddel_term_id = get_field( 'hulpmiddel_detail_select_hulpmiddel_term', $timber_post->post_parent ) ?: 0;
        if ( ! empty( $parent_page_hulpmiddel_term_id ) ) {
            $hulpmiddel_term = get_term( $parent_page_hulpmiddel_term_id, GC_HULPMIDDEL_TAX );
        }
    }
}

// No parent page with Hulpmiddel attached
// See if THIS page has a hulpmiddel attached
if ( ! isset( $hulpmiddel_term ) || ! $hulpmiddel_term instanceof WP_Term ) {
    $page_hulpmiddel_term = wp_get_post_terms( $timber_post->ID, GC_HULPMIDDEL_TAX );
    if ( ! $page_hulpmiddel_term instanceof WP_Error && ! empty( $page_hulpmiddel_term ) ) {
        $hulpmiddel_term = $page_hulpmiddel_term[0];
    }
}

// At this point we'd expect to have a hulpmiddel term
if ( isset( $hulpmiddel_term ) && ! is_wp_error( $hulpmiddel_term ) ) {
	// Update body class
	$context['body_class'] = ( $context['body_class'] ?: '' ) . ' hulpmiddel--' . $hulpmiddel_term->slug;

    // Check if Archive term has 'palette' or 'visual' fields
    // and add it to context so that we can color header
    // Get custom ACF fields for this WP_Term..
    // filter out 'empty' or nullish values.
    $current_hulpmiddel_term_fields = array_filter(
        get_fields( $hulpmiddel_term ) ?: array(),
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
            // Story complete Path to image (if available)
            $context['visual'] = $current_hulpmiddel_term_fields['hulpmiddel_taxonomy_visual'];
            if ( defined( 'GC_HULPMIDDEL_TAX_ASSETS_PATH' ) ) {
                $context['visual'] = sprintf( '%s/images/%s', GC_HULPMIDDEL_TAX_ASSETS_PATH, $context['visual'] );
            }
        }

        // // If we have an extra Hulpmiddel Link
        // if ( isset( $current_hulpmiddel_term_fields['hulpmiddel_taxonomy_link'] ) ) {
        //     $context['hulpmiddel_link'] = $current_hulpmiddel_term_fields['hulpmiddel_taxonomy_link'];
        // }
    }

    // Fallback: Term VISUAL
    if ( ! array_key_exists( 'visual', $context ) ) {
        $context['visual'] = sprintf( '%s/images/', GC_HULPMIDDEL_TAX_ASSETS_PATH ) . 'c-default.svg';
    }

    // Fill context with Hulpmiddel Posts
    $context['items'] = array();

    // Determine the post types we need to filter on
    $post_types    = array( 'post' ); // array with all post types to show
    $posts_per_page = get_option( 'posts_per_page' );
    $args         = array(
        'post_type'      => $post_types,
        'posts_per_page' => $posts_per_page,
        'paged'          => $paged,
        'tax_query'      => array(
            array(
                'taxonomy' => GC_HULPMIDDEL_TAX,
                'field'    => 'slug',
                'terms'    => $hulpmiddel_term->slug,
            ),
        ),
    );

    $context['posts'] = Timber::get_posts( $args );

    if ( count( $context['posts'] ) > 0 ) {
        // Set intro overlap to true, we pull up 1st item into intro
        $context['has_intro_overlap'] = true;

        foreach ( $context['posts'] as $post ) {
            $hulpmiddel_post = prepare_card_content( $post );
            // Hard reset the featured image: we never want it in this archive..
            $hulpmiddel_post['featured_post_image'] = null;
            $context['items'][] = $hulpmiddel_post;
        }
    } else {
        $context['feedbackmessage'] = sprintf( '<p>%s</p>', _x( "Geen berichten gevonden.", 'LLK no content found', 'gctheme' ) );
    }

    // `post_inleiding` field is empty.
    // Update the intro with some hulpmiddel details
    // If it is not yet filled with `post_inleiding` field
    if ( empty( $context['descr'] ) ) {
        // Fallback for when we can not link to hulpmiddel page
        $hulpmiddel_name = sprintf( '<i>%s</i>', $hulpmiddel_term->name );
        // Do we have a hulpmiddel page ID? Link hulpmiddel name instead.
        $hulpmiddel_page_id = $current_hulpmiddel_term_fields['hulpmiddel_taxonomy_page'];
        if ( ! empty( $hulpmiddel_page_id ) ) {
            $hulpmiddel_name = sprintf(
                '<a href="%s">%s</a>',
                get_permalink( $hulpmiddel_page_id ),
                $hulpmiddel_term->name
            );
        }
        $context['descr'] = sprintf(
            '<p>%s</p>',
            $hulpmiddel_term->description ?: sprintf(
                _x( 'Hier vind je artikelen bij het hulpmiddel %s.', 'LLK hulpmiddel archive intro', 'gctheme' ),
                $hulpmiddel_name ?: $hulpmiddel_term->name
            )
        );
    }
} else {
    // No (valid) $hulpmiddel_term found
    // Show a message
    $context['feedbackmessage'] = sprintf( '<p>%s</p>', _x( "Geen berichten gevonden.", 'LLK no content found', 'gctheme' ) );
    // Extra message for editors
    if ( is_user_logged_in() ) {
        $context['feedbackmessage'] .= sprintf( '<p style="color:red">%s</p>', _x( "Er kon geen Hulpmiddel Term worden gevonden. Valt deze pagina wel onder een Hulpmiddel? Zo niet: koppel dan handmatig een Hulpmiddel Term aan deze pagina.", 'LLK no content found, editor message', 'gctheme' ) );
    }
}

Timber::render( $templates, $context );