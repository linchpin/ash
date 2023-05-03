<?php
/**
 * Ash Block Functionality
 *
 * @author  Linchpin
 * @package Ash
 */

namespace Ash\Blocks;

function setup() {
	$n = function ( $function ) {
		return __NAMESPACE__ . "\\$function";
	};

	add_action( 'init', $n( 'register_block_styles' ) );
	add_action( 'init', $n( 'register_block_pattern_categories' ), 9 );
	add_action( 'after_setup_theme', $n( 'after_setup_theme' ) );

}

/**
 * Remove core block patterns
 *
 * @return void
 */
function after_setup_theme() {
	remove_theme_support( 'core-block-patterns' );

}

/**
 * Register block styles.
 *
 * @since 1.1.0
 */
function register_block_styles() {
	$block_styles = array(
		'core/button'          => array(
			'fill-base'    => __( 'Fill Base', 'ash' ),
			'outline-base' => __( 'Outline Base', 'ash' ),
		),
		'core/columns'         => array(
			'columns-reverse' => __( 'Reverse', 'ash' ),
		),
		'core/group'           => array(
			'shadow'       => __( 'Shadow', 'ash' ),
			'shadow-solid' => __( 'Shadow Solid', 'ash' ),
		),
		'core/list'            => array(
			'no-disc' => __( 'No Disc', 'ash' ),
		),
		'core/navigation-link' => array(
			'outline' => __( 'Outline', 'ash' ),
		),
		'core/social-links'    => array(
			'outline' => __( 'Outline', 'ash' ),
		),
	);

	foreach ( $block_styles as $block => $styles ) {
		foreach ( $styles as $style_name => $style_label ) {
			register_block_style(
				$block,
				array(
					'name'  => $style_name,
					'label' => $style_label,
				)
			);
		}
	}
}

/**
 * Registers block categories, and type.
 *
 * @since 0.9.2
 */
function register_block_pattern_categories() {
	/* Functionality specific to the Block Pattern Explorer plugin. */
	if ( function_exists( 'register_block_pattern_category_type' ) ) {
		register_block_pattern_category_type( 'frost', array( 'label' => __( 'Ash', 'ash' ) ) );
	}

	$block_pattern_categories = array(
		'frost-footer'    => array(
			'label'         => __( 'Ash', 'ash' ),
			'categoryTypes' => array( 'ash' ),
		),
		'frost-general'   => array(
			'label'         => __( 'General', 'ash' ),
			'categoryTypes' => array( 'ash' ),
		),
		'frost-header'    => array(
			'label'         => __( 'Header', 'ash' ),
			'categoryTypes' => array( 'ash' ),
		),
		'frost-page'      => array(
			'label'         => __( 'Page', 'ash' ),
			'categoryTypes' => array( 'ash' ),
		),
		'frost-query'     => array(
			'label'         => __( 'Query', 'ash' ),
			'categoryTypes' => array( 'ash' ),
		),
		'frost-proposals' => array(
			'label'         => __( 'Proposals', 'ash' ),
			'categoryTypes' => array( 'ash' ),
		),
	);

	foreach ( $block_pattern_categories as $name => $properties ) {
		register_block_pattern_category( $name, $properties );
	}

}
