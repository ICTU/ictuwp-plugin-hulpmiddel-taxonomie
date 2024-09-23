<?php
/**
 * GC ACF Fields for: Hulpmiddel Taxonomy
 *
 * ACF fields for `hulpmiddel` taxonomy
 *
 * @see https://www.advancedcustomfields.com/resources/register-fields-via-php/
 *
 * - It is important to remember that each field group’s key and each field’s key must be unique.
 * The key is a reference for ACF to find, save and load data. If 2 fields or 2 groups are added using
 * the same key, the later will override the original.
 *
 * - Field Groups and Fields registered via code will NOT be visible/editable via
 * the “Edit Field Groups” admin page.
 *
 * Initialize with eg:
 * add_action('acf/init', 'my_acf_add_local_field_groups');
 *
 */

if ( ! function_exists( 'acf_add_local_field_group' ) ) {
	return;
}

// OPTIONAL: use current flavor
// ----------------------------
// $get_theme_option = get_option( 'gc2020_theme_options' );
// $currentflavor    = isset( $get_theme_option['flavor_select'] ) ? $get_theme_option['flavor_select'] : 'GC';
// ----------------------------

// Available Taxonomy visuals
// ----------------------------
// Base path for existing hulpmiddel taxonomy visuals
$visuals_base_img = '<img src="' . GC_HULPMIDDEL_TAX_VISUALS_PATH . '/%s" width="50" height="50" class="hulpmiddel-visual" alt="" />%s';

$visuals = array(
	'none' => 'Geen beeldmerk',
);
$available_visuals = glob( __DIR__ . '/../assets/images/beeldmerken/*.svg' );

if( $available_visuals ) {

	foreach ( $available_visuals as $key => $val ) {

		// Skip existing default
		if ( $val !== 'none' ) {

			$visual_filename = preg_replace( '/.*\/assets\/images\/beeldmerken\//i', '', $val );

			if ( $visual_filename ) {

				// $visual_filekey = str_replace( array( 'c-', '.svg' ), '', $visual_filename );
				// $visuals[$visual_filekey] = $visual_filename;

				$visual_label = str_replace( array( 'beeldmerk-', '.svg' ), '', $visual_filename );
				$visual_label = str_replace( '-', ' ', $visual_label );
				$visual_label = ucwords( $visual_label );

				// Specific Exceptions
				// 'Default' => 'Standaard',
				if ( $visual_label == 'Default' ) { $visual_label = 'Standaard'; }
				// Two-letter acronyms? => Uppercase
				if ( strlen( $visual_label ) == 2 ) { $visual_label = strtoupper( $visual_label ); }

				$visuals[$visual_filename] = addslashes( sprintf( $visuals_base_img, $visual_filename, $visual_label ) );

			}

		}

	}

}

// Setup available color schemes
// ----------------------------
$color_themes = array(
	'default' => '<span class="swatch swatch--green">Standaard</span>',
);
if ( function_exists( 'gc_get_colorschemes' ) ) {
	$available_color_themes = gc_get_colorschemes();
	foreach ( $available_color_themes as $key => $val ) {
		$color_themes[ $key ] = '<span class="swatch swatch--' . $key . '">' . $val['name'] . ' ' . _x( 'Kleurenschema', 'Hulpmiddel taxonomy ACF field definition', 'gctheme' ) . '</span>';
	}

	// hardcode
	// If we have a `green` color, set that as default
	// and remove the `default` color
	if ( isset( $color_themes['green'] ) ) {
		unset( $color_themes['default'] );
	}

}

// Add the field group

acf_add_local_field_group( array(
	'key' => 'group_66cca4428ac9d',
	'title' => 'GC - Hulpmiddel taxonomy',
	'fields' => array(
		array(
			'key' => 'field_66cca443ef1fe',
			'label' => 'Hulpmiddelpagina',
			'name' => 'hulpmiddel_taxonomy_page',
			'aria-label' => '',
			'type' => 'post_object',
			'instructions' => 'Deze pagina zal worden getoond als een overzichtspagina met alle informatie over het hulpmiddel.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'post_type' => array(
				0 => 'page',
			),
			'post_status' => '',
			'taxonomy' => '',
			'return_format' => 'id',
			'allow_null' => 0,
			'multiple' => 0,
			'bidirectional' => 1,
			'bidirectional_target' => array(
				0 => 'field_66cca921173b2',
			),
			'ui' => 1,
		),
		array(
			'key' => 'field_66cca64eef202',
			'label' => 'Uitgelichte afbeelding',
			'name' => 'hulpmiddel_taxonomy_featured_image',
			'aria-label' => '',
			'type' => 'image',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'return_format' => 'array',
			'library' => 'all',
			'min_width' => '',
			'min_height' => '',
			'min_size' => '',
			'max_width' => '',
			'max_height' => '',
			'max_size' => '',
			'mime_types' => '',
			'preview_size' => 'image-16x9',
		),
		array(
			'key' => 'field_66cca50fef1ff',
			'label' => 'Colorscheme',
			'name' => 'hulpmiddel_taxonomy_colorscheme',
			'aria-label' => '',
			'type' => 'radio',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'choices' => $color_themes,
			'default_value' => isset( $color_themes['green'] ) ? 'green' : 'default',
			'return_format' => 'value',
			'allow_null' => 0,
			'other_choice' => 0,
			'layout' => 'vertical',
			'save_other_choice' => 0,
		),
		array(
			'key' => 'field_66cca56eef200',
			'label' => 'Visual',
			'name' => 'hulpmiddel_taxonomy_visual',
			'aria-label' => '',
			'type' => 'radio',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'choices' => $visuals,
			'default_value' => 'default',
			'return_format' => 'value',
			'allow_null' => 0,
			'other_choice' => 0,
			'layout' => 'vertical',
			'save_other_choice' => 0,
		),
		array(
			'key' => 'field_66cca5afef201',
			'label' => 'Link',
			'name' => 'hulpmiddel_taxonomy_link',
			'aria-label' => '',
			'type' => 'link',
			'instructions' => '(Optionele) link naar bijvoorbeeld een subsite',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'return_format' => 'array',
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'taxonomy',
				'operator' => '==',
				'value' => GC_HULPMIDDEL_TAX,
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


// Add the field group: 'Metabox: (45) Hulpmiddel Richtlijnen tonen'
// definitions taken from:
// [themes]/ictuwp-theme-gc2020/acf-json/group_66ec33e7e89d5.json.original
//
// Metabox order for template-detail-hulpmiddelen.php
// this number determines $menu_order
//
// 10 - Intro-tekst
// 20 - info / USP ("Wat houdt het in?")
// 30 - Events
// 40 - berichten
// 45 - richtlijnen <--
// 50 - profielen
// 60 - formulier
// 70 - partners
// ----------------------------
acf_add_local_field_group( array(
	'key' => 'group_66ec33e7e89d5',
	'title' => 'Metabox: (45) Hulpmiddel Richtlijnen tonen',
	'fields' => array(
		array(
			'key' => 'field_66ec33e8e4da2',
			'label' => 'Gerelateerde richtlijnen',
			'name' => 'richtlijnen',
			'aria-label' => '',
			'type' => 'group',
			'instructions' => 'Toon of verberg het blok met richtlijnen voor deze pagina.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'layout' => 'block',
			'sub_fields' => array(
				array(
					'key' => 'field_66ec33e8e7237',
					'label' => 'Richtlijnen tonen voor dit hulpmiddel?',
					'name' => 'metabox_hulpmiddel_richtlijnen_show_or_not',
					'aria-label' => '',
					'type' => 'radio',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'choices' => array(
						'ja' => 'Ja',
						'nee' => 'Nee',
					),
					'default_value' => 'nee',
					'return_format' => 'value',
					'allow_null' => 0,
					'other_choice' => 0,
					'allow_in_bindings' => 1,
					'layout' => 'horizontal',
					'save_other_choice' => 0,
				),
				array(
					'key' => 'field_66ec33e8e723f',
					'label' => 'Titel',
					'name' => 'metabox_hulpmiddel_richtlijnen_titel',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => array(
						array(
							array(
								'field' => 'field_66ec33e8e7237',
								'operator' => '==',
								'value' => 'ja',
							),
						),
					),
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => 'Hulp nodig?',
					'maxlength' => '',
					'allow_in_bindings' => 1,
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
				),
				array(
					'key' => 'field_66ec33e8e7246',
					'label' => 'Omschrijving',
					'name' => 'metabox_hulpmiddel_richtlijnen_omschrijving',
					'aria-label' => '',
					'type' => 'textarea',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => array(
						array(
							array(
								'field' => 'field_66ec33e8e7237',
								'operator' => '==',
								'value' => 'ja',
							),
						),
					),
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'maxlength' => '',
					'allow_in_bindings' => 0,
					'rows' => 4,
					'placeholder' => '',
					'new_lines' => 'wpautop',
				),
				array(
					'key' => 'field_66ec33e8e724d',
					'label' => 'Overzichtslink',
					'name' => 'metabox_hulpmiddel_richtlijnen_url_overview',
					'aria-label' => '',
					'type' => 'link',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => array(
						array(
							array(
								'field' => 'field_66ec33e8e7237',
								'operator' => '==',
								'value' => 'ja',
							),
						),
					),
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'return_format' => 'array',
					'allow_in_bindings' => 1,
				),
				array(
					'key' => 'field_66ec33e8e7253',
					'label' => 'Sectie stijl',
					'name' => 'metabox_hulpmiddel_richtlijnen_section_style',
					'aria-label' => '',
					'type' => 'radio',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => array(
						array(
							array(
								'field' => 'field_66ec33e8e7237',
								'operator' => '==',
								'value' => 'ja',
							),
						),
					),
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'choices' => array(
						'default' => 'Standaard',
						'background' => 'Met achtergrond',
					),
					'default_value' => 'background',
					'return_format' => 'value',
					'multiple' => 0,
					'allow_null' => 0,
					'allow_in_bindings' => 0,
					'ui' => 0,
					'ajax' => 0,
					'placeholder' => '',
				),
			),
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
	'menu_order' => 40,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => true,
	'description' => '',
	'show_in_rest' => 0,
) );


// ----------------------------


/**
 * The following appends CSS styles to the global
 * ACF styles. This is needed to customize our added <images>
 * (Somehow adding these styles to editor-styles did not work?)
 */
function gc_add_hulpmiddel_tax_admin_css () {
	$css = '
		.hulpmiddel-taxonomy-visual,
		.swatch::before {
			display: inline-block;
			vertical-align: middle;
			margin-inline-end: .5rem;
		}
		.swatch::before {
			content: "";
			width: 32px;
			height: 32px;
			border: 2px solid white;
			background-color: white;
			background-image: linear-gradient(-90deg, white 30%, black 30%);
		}
		.swatch--green::before { background-image: linear-gradient(-90deg, #148839 30%, #148839 30%); }

		:is([data-name="hulpmiddel_taxonomy_visual"], [data-name="hulpmiddel_taxonomy_colorscheme"]) ul.acf-radio-list li label {
			display: flex;
			align-items: center;
			gap: 1em;
			padding: .5em;
			min-height: 50px;
			border: 1px solid rgb(240, 240, 241);
		}
		:is([data-name="hulpmiddel_taxonomy_visual"], [data-name="hulpmiddel_taxonomy_colorscheme"]) ul.acf-radio-list label:has(:checked) {
			background-color: #F0F6FC;
			border-color: #2271b1;
		}

	';

	// Dynamically add available colors
	if ( function_exists( 'gc_get_colorschemes' ) ) {
		$available_color_themes = gc_get_colorschemes();
			foreach ( $available_color_themes as $key => $val ) {
			$css .= '.swatch--' . $key . '::before { background-image: linear-gradient(-90deg, ' . $val['primary']['color'] . ' 30%, ' . $val['secondary']['color'] . ' 30%); }';
		}

	}

	wp_add_inline_style( 'acf-global', trim($css) );
}

add_action( 'admin_enqueue_scripts', 'gc_add_hulpmiddel_tax_admin_css' );